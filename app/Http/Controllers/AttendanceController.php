<?php

namespace App\Http\Controllers;

use App\Models\EmployeeAttendance;
use App\Models\EmployeeTest;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class AttendanceController extends Controller
{
    public function records(Request $request)
    {
        $date = $request->query('date');
        $dateValue = $date ? Carbon::parse($date) : Carbon::today();
        $user = Auth::user();
        if (!$user) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $employee = $this->getEmployeeForUser($user);


        $rows = EmployeeAttendance::query()
            ->where('employee_id', $employee->id)
            ->when($date, function ($query) use ($dateValue) {
                $query->whereDate('attendance_date', $dateValue->toDateString());
            })
            ->orderByDesc('attendance_date')
            ->get()
            ->map(function ($row) {
                $dateValue = Carbon::parse($row->attendance_date);
                $status = 'time-in';
                if (!$row->time_in && !$row->time_out) {
                    $status = 'absent';
                } elseif ($row->time_out) {
                    $status = 'clocked-out';
                }
                $timeInPhoto = $row->time_in_photo_path
                    ? asset('storage/' . $row->time_in_photo_path)
                    : null;
                $timeOutPhoto = $row->time_out_photo_path
                    ? asset('storage/' . $row->time_out_photo_path)
                    : null;
                return [
                    'date' => $dateValue->toDateString(),
                    'dayOfWeek' => $dateValue->format('l'),
                    'timeIn' => $row->time_in,
                    'timeOut' => $row->time_out,
                    'status' => $status,
                    'timeInPhoto' => $timeInPhoto,
                    'timeOutPhoto' => $timeOutPhoto,
                    'qrPayload' => $row->qr_payload
                ];
            });

        return response()->json([
            'data' => $rows,
            'meta' => [
                'date' => $dateValue->toDateString(),
                'dayOfWeek' => $dateValue->format('l'),
                'employee' => [
                    'id' => $employee->id,
                    'name' => $employee->name,
                    'position' => $employee->position
                ]
            ]
        ]);
    }

    public function updateRecord(Request $request, string $date)
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $employee = $this->getEmployeeForUser($user);
        $validated = $request->validate([
            'time_in' => ['nullable', 'regex:/^\d{2}:\d{2}(:\d{2})?$/'],
            'time_out' => ['nullable', 'regex:/^\d{2}:\d{2}(:\d{2})?$/'],
            'qr_payload' => ['nullable', 'string'],
            'time_in_photo' => ['nullable', 'string'],
            'time_out_photo' => ['nullable', 'string']
        ]);

        $timeIn = $validated['time_in'] ?? null;
        $timeOut = $validated['time_out'] ?? null;

        if ($timeIn === '') {
            $timeIn = null;
        }

        if ($timeOut === '') {
            $timeOut = null;
        }

        if ($timeIn && strlen($timeIn) === 5) {
            $timeIn .= ':00';
        }

        if ($timeOut && strlen($timeOut) === 5) {
            $timeOut .= ':00';
        }

        $lastAttendance = EmployeeAttendance::query()
            ->where('employee_id', $employee->id)
            ->orderByDesc('attendance_date')
            ->first();

        $timeInPhotoPath = $this->saveBase64Image($validated['time_in_photo'] ?? null, 'time_in');
        $timeOutPhotoPath = $this->saveBase64Image($validated['time_out_photo'] ?? null, 'time_out');

        $updateData = [
            'payroll_start' => $lastAttendance?->payroll_start?->toDateString() ?? $date,
            'payroll_end' => $lastAttendance?->payroll_end?->toDateString() ?? $date
        ];

        if ($request->has('time_in')) {
            $updateData['time_in'] = $timeIn;
        }
        if ($request->has('time_out')) {
            $updateData['time_out'] = $timeOut;
        }

        if (!empty($validated['qr_payload'])) {
            $updateData['qr_payload'] = $validated['qr_payload'];
        }
        if ($timeInPhotoPath) {
            $updateData['time_in_photo_path'] = $timeInPhotoPath;
        }
        if ($timeOutPhotoPath) {
            $updateData['time_out_photo_path'] = $timeOutPhotoPath;
        }

        EmployeeAttendance::updateOrCreate(
            [
                'employee_id' => $employee->id,
                'attendance_date' => $date
            ],
            $updateData
        );

        return response()->json(['status' => 'ok']);
    }

    public function qrCode(Request $request)
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $employee = $this->getEmployeeForUser($user);
        $date = $request->query('date')
            ? Carbon::parse($request->query('date'))->toDateString()
            : Carbon::today()->toDateString();

        $payload = json_encode([
            'type' => 'employee_attendance',
            'employee_id' => $employee->id,
            'date' => $date
        ], JSON_UNESCAPED_SLASHES);

        $lastAttendance = EmployeeAttendance::query()
            ->where('employee_id', $employee->id)
            ->orderByDesc('attendance_date')
            ->first();

        EmployeeAttendance::updateOrCreate(
            [
                'employee_id' => $employee->id,
                'attendance_date' => $date
            ],
            [
                'qr_payload' => $payload,
                'payroll_start' => $lastAttendance?->payroll_start?->toDateString() ?? $date,
                'payroll_end' => $lastAttendance?->payroll_end?->toDateString() ?? $date
            ]
        );

        return response()->json([
            'data' => [
                'date' => $date,
                'payload' => $payload
            ]
        ]);
    }

    private function getEmployeeForUser($user): EmployeeTest
    {
        return EmployeeTest::firstOrCreate(
            ['user_id' => $user->id],
            [
                'position' => 'Employee',
                'rate' => 80,
                'payroll_frequency' => 'Weekly'
            ]
        );
    }

    private function saveBase64Image(?string $data, string $prefix): ?string
    {
        if (!$data) {
            return null;
        }

        if (!preg_match('/^data:image\/(\w+);base64,/', $data, $matches)) {
            return null;
        }

        $extension = strtolower($matches[1]);
        $base64 = substr($data, strpos($data, ',') + 1);
        $binary = base64_decode($base64, true);

        if ($binary === false) {
            return null;
        }

        $filename = sprintf('%s_%s_%s.%s', $prefix, now()->format('Ymd_His'), Str::random(8), $extension);
        $path = 'attendance/' . $filename;

        Storage::disk('public')->put($path, $binary);

        return $path;
    }
}
