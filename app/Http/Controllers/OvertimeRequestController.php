<?php

namespace App\Http\Controllers;

use App\Models\EmployeeTest;
use App\Models\OvertimeRate;
use App\Models\OvertimeRequest;
use App\Models\EmployeeSchedule;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OvertimeRequestController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $employee = $this->getEmployeeForUser($user);
        $rates = OvertimeRate::query()->get()->keyBy('day_type');

        $rows = OvertimeRequest::query()
            ->where('employee_id', $employee->id)
            ->orderByDesc('request_date')
            ->get()
            ->map(function ($item) use ($rates) {
                $rate = $rates->get($item->day_type);
                return [
                    'id' => $item->id,
                    'date' => $item->request_date?->toDateString(),
                    'hours' => $item->hours,
                    'startTime' => $item->start_time,
                    'dayType' => $item->day_type,
                    'dayTypeLabel' => $rate?->label ?? $item->day_type,
                    'status' => $item->status,
                    'reason' => $item->reason,
                    'requestedAt' => $item->created_at?->toDateTimeString(),
                    'approvedAt' => $item->approved_at?->toDateTimeString(),
                    'approvedMultiplier' => $item->approved_multiplier,
                    'approvedHours' => $item->approved_hours
                ];
            });

        return response()->json(['data' => $rows]);
    }

    public function store(Request $request)
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $employee = $this->getEmployeeForUser($user);
        $validated = $request->validate([
            'request_date' => ['required', 'date'],
            'hours' => ['required', 'numeric', 'min:0.5', 'max:24'],
            'day_type' => ['required', 'string', 'exists:overtime_rates,day_type'],
            'start_time' => ['required', 'regex:/^\d{2}:\d{2}(:\d{2})?$/'],
            'reason' => ['nullable', 'string', 'max:1000']
        ]);

        $requestDate = Carbon::parse($validated['request_date']);
        $minDate = Carbon::today()->addDay();
        if ($requestDate->lessThan($minDate)) {
            return response()->json([
                'message' => 'Overtime requests must be filed at least one day ahead.'
            ], 422);
        }

        $rate = OvertimeRate::query()
            ->where('day_type', $validated['day_type'])
            ->where('is_active', true)
            ->first();

        if (!$rate) {
            return response()->json([
                'message' => 'Selected overtime day type is not active.'
            ], 422);
        }

        $requestRow = OvertimeRequest::create([
            'employee_id' => $employee->id,
            'request_date' => $requestDate->toDateString(),
            'hours' => $validated['hours'],
            'day_type' => $validated['day_type'],
            'start_time' => $validated['start_time'],
            'status' => 'Pending',
            'reason' => $validated['reason'] ?? null,
            'requested_by' => $user->id
        ]);

        return response()->json([
            'data' => $requestRow
        ], 201);
    }

    public function pending()
    {
        $rates = OvertimeRate::query()->get()->keyBy('day_type');

        $rows = OvertimeRequest::query()
            ->with(['employee.user', 'requestedBy'])
            ->where('status', 'Pending')
            ->orderBy('request_date')
            ->get()
            ->map(function ($item) use ($rates) {
                $rate = $rates->get($item->day_type);
                return [
                    'id' => $item->id,
                    'date' => $item->request_date?->toDateString(),
                    'hours' => $item->hours,
                    'startTime' => $item->start_time,
                    'dayType' => $item->day_type,
                    'dayTypeLabel' => $rate?->label ?? $item->day_type,
                    'multiplier' => $rate?->multiplier ?? null,
                    'status' => $item->status,
                    'reason' => $item->reason,
                    'employee' => [
                        'id' => $item->employee?->id,
                        'name' => $item->employee?->name,
                        'position' => $item->employee?->position
                    ],
                    'requestedBy' => [
                        'id' => $item->requestedBy?->id,
                        'name' => $item->requestedBy?->first_name
                            ? trim($item->requestedBy->first_name . ' ' . $item->requestedBy->last_name)
                            : $item->requestedBy?->email
                    ],
                    'requestedAt' => $item->created_at?->toDateTimeString()
                ];
            });

        return response()->json(['data' => $rows]);
    }

    public function approve(OvertimeRequest $overtimeRequest)
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        if ($overtimeRequest->status !== 'Pending') {
            return response()->json([
                'message' => 'Overtime request is no longer pending.'
            ], 409);
        }

        $schedule = EmployeeSchedule::query()
            ->where('employee_id', $overtimeRequest->employee_id)
            ->first();

        if (!$schedule) {
            return response()->json([
                'message' => 'No schedule found for this employee.'
            ], 422);
        }

        $shiftEnd = Carbon::createFromFormat('H:i:s', $schedule->end_time);
        $requestStart = Carbon::createFromFormat('H:i:s', $overtimeRequest->start_time);
        $requestEnd = (clone $requestStart)->addMinutes((float) $overtimeRequest->hours * 60);

        if ($requestStart->lessThan($shiftEnd)) {
            return response()->json([
                'message' => 'Overtime must start after the scheduled shift end time.'
            ], 422);
        }

        $attendance = $overtimeRequest->employee
            ? $overtimeRequest->employee->attendances()
                ->whereDate('attendance_date', $overtimeRequest->request_date)
                ->first()
            : null;

        if (!$attendance || !$attendance->time_out) {
            $approvedHours = round((float) $overtimeRequest->hours, 2);
        } else {
            $timeOut = Carbon::createFromFormat('H:i:s', $attendance->time_out);
            $approvedStart = $requestStart->greaterThan($shiftEnd) ? $requestStart : $shiftEnd;
            $approvedEnd = $requestEnd->lessThan($timeOut) ? $requestEnd : $timeOut;

            if ($approvedEnd->lessThanOrEqualTo($approvedStart)) {
                return response()->json([
                    'message' => 'No overtime hours overlap with actual attendance.'
                ], 422);
            }

            $approvedMinutes = $approvedEnd->diffInMinutes($approvedStart);
            $approvedHours = round($approvedMinutes / 60, 2);
        }

        $rate = OvertimeRate::query()
            ->where('day_type', $overtimeRequest->day_type)
            ->first();

        $overtimeRequest->update([
            'status' => 'Approved',
            'approved_by' => $user->id,
            'approved_at' => now(),
            'approved_multiplier' => $rate?->multiplier,
            'approved_hours' => $approvedHours
        ]);

        return response()->json(['data' => $overtimeRequest]);
    }

    public function reject(OvertimeRequest $overtimeRequest)
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        if ($overtimeRequest->status !== 'Pending') {
            return response()->json([
                'message' => 'Overtime request is no longer pending.'
            ], 409);
        }

        $overtimeRequest->update([
            'status' => 'Rejected',
            'approved_by' => $user->id,
            'approved_at' => now()
        ]);

        return response()->json(['data' => $overtimeRequest]);
    }

    public function cancel(OvertimeRequest $overtimeRequest)
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $employee = $this->getEmployeeForUser($user);
        if ($overtimeRequest->employee_id !== $employee->id) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        if ($overtimeRequest->status !== 'Pending') {
            return response()->json([
                'message' => 'Only pending requests can be cancelled.'
            ], 409);
        }

        $overtimeRequest->update([
            'status' => 'Cancelled'
        ]);

        return response()->json(['data' => $overtimeRequest]);
    }

    private function getEmployeeForUser($user): EmployeeTest
    {
        return EmployeeTest::firstOrCreate(
            ['user_id' => $user->id],
            [
                'position' => 'Employee',
                'rate' => 80
            ]
        );
    }
}
