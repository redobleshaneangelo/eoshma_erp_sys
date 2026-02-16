<?php

namespace App\Http\Controllers;

use App\Models\EmployeeAttendance;
use App\Models\EmployeeTest;
use App\Models\LeaveRequest;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TimekeepingController extends Controller
{
    public function days(Request $request)
    {
        $month = (int) $request->query('month', now()->month);
        $year = (int) $request->query('year', now()->year);
        $status = trim((string) $request->query('status', ''));
        $search = trim((string) $request->query('search', ''));

        $periodStart = Carbon::createFromDate($year, $month, 1)->startOfDay();
        $periodEnd = $periodStart->copy()->endOfMonth();

        if ($search !== '') {
            $searchYear = $this->extractSearchYear($search) ?: $year;
            $periodStart = Carbon::createFromDate($searchYear, 1, 1)->startOfDay();
            $periodEnd = $periodStart->copy()->endOfYear();
        }

        $employees = EmployeeTest::query()
            ->select('id', 'user_id')
            ->get();

        $employeeIds = $employees->pluck('id')->map(fn ($id) => (int) $id)->all();
        $totalEmployees = count($employeeIds);

        $attendances = EmployeeAttendance::query()
            ->select('employee_id', 'attendance_date', 'time_in', 'time_out')
            ->whereBetween('attendance_date', [$periodStart->toDateString(), $periodEnd->toDateString()])
            ->get();

        $attendanceLookup = [];
        foreach ($attendances as $attendance) {
            $dateKey = Carbon::parse($attendance->attendance_date)->toDateString();
            $attendanceLookup[$dateKey][(int) $attendance->employee_id] = [
                'time_in' => $attendance->time_in,
                'time_out' => $attendance->time_out,
            ];
        }

        $leaveLookup = $this->approvedLeaveLookupByDateAndEmployee($employees, $periodStart, $periodEnd);
        $today = Carbon::today()->toDateString();

        $rows = collect();
        $cursor = $periodStart->copy();

        while ($cursor->lte($periodEnd)) {
            $dateKey = $cursor->toDateString();

            if (!$this->matchesDateSearch($search, $dateKey, $year)) {
                $cursor->addDay();
                continue;
            }

            $presentCount = 0;
            $lateCount = 0;
            $awolCount = 0;
            $onLeaveCount = 0;
            $noAttendanceCount = 0;

            foreach ($employeeIds as $employeeId) {
                $attendance = $attendanceLookup[$dateKey][$employeeId] ?? null;
                $isOnLeave = !empty($leaveLookup[$dateKey][$employeeId]);
                $employeeStatus = $this->resolveAttendanceStatus($dateKey, $today, $attendance, $isOnLeave);

                match ($employeeStatus) {
                    'present' => $presentCount++,
                    'late' => $lateCount++,
                    'awol' => $awolCount++,
                    'on_leave' => $onLeaveCount++,
                    default => $noAttendanceCount++,
                };
            }

            $rows->push([
                'date' => $dateKey,
                'dayOfWeek' => $cursor->format('l'),
                'status' => $this->resolveDayStatus($presentCount, $lateCount, $awolCount, $onLeaveCount, $noAttendanceCount),
                'recordCount' => $totalEmployees,
                'presentCount' => $presentCount,
                'lateCount' => $lateCount,
                'awolCount' => $awolCount,
                'onLeaveCount' => $onLeaveCount,
                'noAttendanceCount' => $noAttendanceCount,
            ]);

            $cursor->addDay();
        }

        $rows = $rows->sortByDesc('date')->values();

        if ($status) {
            $rows = $rows->filter(function ($row) use ($status) {
                return $row['status'] === $status;
            })->values();
        }

        return response()->json(['data' => $rows]);
    }

    public function dayDetail(Request $request, string $date)
    {
        $perPage = (int) $request->query('per_page', 10);
        $page = (int) $request->query('page', 1);
        $status = trim((string) $request->query('status', ''));
        $search = trim((string) $request->query('search', ''));

        $approvedLeaveUsers = LeaveRequest::query()
            ->select('user_id')
            ->where('status', 'approved')
            ->whereDate('start_date', '<=', $date)
            ->whereDate('end_date', '>=', $date)
            ->distinct();

        $today = Carbon::today()->toDateString();

        $nameExpression = "TRIM(CONCAT(users.last_name, ', ', users.first_name, IF(users.middle_name IS NULL OR users.middle_name = '', '', CONCAT(' ', LEFT(users.middle_name, 1), '.'))))";

        $baseQuery = EmployeeTest::query()
            ->leftJoin('users', 'employees.user_id', '=', 'users.id')
            ->leftJoinSub($approvedLeaveUsers, 'lr', function ($join) {
                $join->on('employees.user_id', '=', 'lr.user_id');
            })
            ->leftJoin('employee_attendances as ea', function ($join) use ($date) {
                $join->on('employees.id', '=', 'ea.employee_id')
                    ->whereDate('ea.attendance_date', $date);
            })
            ->select(
                'employees.id',
                DB::raw("{$nameExpression} as name"),
                'employees.position',
                'ea.time_in',
                'ea.time_out',
                'ea.time_in_location_label',
                'ea.time_out_location_label',
                'ea.time_in_latitude',
                'ea.time_in_longitude',
                'ea.time_out_latitude',
                'ea.time_out_longitude',
                DB::raw('CASE WHEN lr.user_id IS NULL THEN 0 ELSE 1 END as is_on_leave')
            )
            ->orderByRaw("{$nameExpression} asc");

        if ($search) {
            $baseQuery->where(function ($q) use ($search, $nameExpression) {
                $q->whereRaw("{$nameExpression} like ?", ['%' . $search . '%'])
                    ->orWhere('employees.id', 'like', '%' . $search . '%');
            });
        }

        $allItems = $baseQuery->get()->map(function ($item) use ($date, $today) {
            $resolvedStatus = $this->resolveAttendanceStatus(
                $date,
                $today,
                [
                    'time_in' => $item->time_in,
                    'time_out' => $item->time_out,
                ],
                (bool) $item->is_on_leave
            );

            return [
                'id' => $item->id,
                'name' => $item->name,
                'position' => $item->position,
                'timeIn' => $item->time_in,
                'timeOut' => $item->time_out,
                'timeInLocation' => $item->time_in_location_label ?: $this->formatCoordinateLocation($item->time_in_latitude, $item->time_in_longitude),
                'timeOutLocation' => $item->time_out_location_label ?: $this->formatCoordinateLocation($item->time_out_latitude, $item->time_out_longitude),
                'status' => $resolvedStatus
            ];
        });

        $summaryRows = $allItems->groupBy('status')->map->count();

        $filtered = $status
            ? $allItems->filter(fn ($item) => $item['status'] === $status)->values()
            : $allItems->values();

        $total = $filtered->count();
        $lastPage = max(1, (int) ceil($total / max(1, $perPage)));
        $page = max(1, min($page, $lastPage));
        $offset = ($page - 1) * $perPage;
        $items = $filtered->slice($offset, $perPage)->values();

        $dateValue = Carbon::parse($date);

        return response()->json([
            'data' => $items,
            'meta' => [
                'current_page' => $page,
                'last_page' => $lastPage,
                'per_page' => $perPage,
                'total' => $total
            ],
            'summary' => [
                'date' => $dateValue->toDateString(),
                'dayOfWeek' => $dateValue->format('l'),
                'presentCount' => (int) ($summaryRows['present'] ?? 0),
                'lateCount' => (int) ($summaryRows['late'] ?? 0),
                'awolCount' => (int) ($summaryRows['awol'] ?? 0),
                'onLeaveCount' => (int) ($summaryRows['on_leave'] ?? 0),
                'noAttendanceCount' => (int) ($summaryRows['no_attendance'] ?? 0)
            ]
        ]);
    }

    private function approvedLeaveLookupByDateAndEmployee($employees, Carbon $start, Carbon $end): array
    {
        $employeesByUserId = $employees
            ->filter(fn ($employee) => !empty($employee->user_id))
            ->keyBy('user_id');

        if ($employeesByUserId->isEmpty()) {
            return [];
        }

        $approvedLeaves = LeaveRequest::query()
            ->where('status', 'approved')
            ->whereIn('user_id', $employeesByUserId->keys()->all())
            ->whereDate('start_date', '<=', $end->toDateString())
            ->whereDate('end_date', '>=', $start->toDateString())
            ->get();

        $lookup = [];
        foreach ($approvedLeaves as $leave) {
            $employee = $employeesByUserId->get((int) $leave->user_id);
            if (!$employee) {
                continue;
            }

            $cursor = Carbon::parse($leave->start_date);
            $leaveEnd = Carbon::parse($leave->end_date);

            if ($cursor->lt($start)) {
                $cursor = $start->copy();
            }

            if ($leaveEnd->gt($end)) {
                $leaveEnd = $end->copy();
            }

            while ($cursor->lte($leaveEnd)) {
                $lookup[$cursor->toDateString()][(int) $employee->id] = true;
                $cursor->addDay();
            }
        }

        return $lookup;
    }

    private function resolveAttendanceStatus(string $attendanceDate, string $today, ?array $attendance, bool $isOnLeave): string
    {
        if ($isOnLeave) {
            return 'on_leave';
        }

        $timeIn = $attendance['time_in'] ?? null;
        $timeOut = $attendance['time_out'] ?? null;

        if ($timeIn || $timeOut) {
            if ($timeIn && $timeIn > '08:05:00') {
                return 'late';
            }

            return 'present';
        }

        if ($attendanceDate < $today) {
            return 'awol';
        }

        return 'no_attendance';
    }

    private function resolveDayStatus(int $present, int $late, int $awol, int $onLeave, int $noAttendance): string
    {
        if ($awol > 0) {
            return 'awol';
        }

        if ($late > 0) {
            return 'late';
        }

        if ($onLeave > 0) {
            return 'on_leave';
        }

        if ($noAttendance > 0) {
            return 'no_attendance';
        }

        return 'present';
    }

    private function matchesDateSearch(string $search, string $date, int $year): bool
    {
        if ($search === '') {
            return true;
        }

        $needle = $this->normalizeSearchTerm($search);
        $dateValue = Carbon::parse($date);

        $candidates = [
            $dateValue->toDateString(),
            $dateValue->format('Y-m'),
            $dateValue->format('m/d/Y'),
            $dateValue->format('n/j/Y'),
            $dateValue->format('F j, Y'),
            $dateValue->format('F j Y'),
            $dateValue->format('M j, Y'),
            $dateValue->format('M j Y'),
            $dateValue->format('F'),
            $dateValue->format('M'),
            $dateValue->format('l'),
            $dateValue->format('D'),
            $dateValue->format('F Y'),
            $dateValue->format('M Y'),
            $dateValue->format('F j'),
            $dateValue->format('M j'),
            $dateValue->format('j'),
            $dateValue->format('d'),
            $dateValue->format('Y'),
        ];

        foreach ($candidates as $candidate) {
            $normalizedCandidate = $this->normalizeSearchTerm((string) $candidate);
            if (str_contains($normalizedCandidate, $needle)) {
                return true;
            }
        }

        $tokens = array_values(array_filter(explode(' ', $needle)));
        if (count($tokens) <= 1) {
            return false;
        }

        $candidateBlob = $this->normalizeSearchTerm(implode(' ', $candidates));
        foreach ($tokens as $token) {
            if (!str_contains($candidateBlob, $token)) {
                return false;
            }
        }

        return true;
    }

    private function normalizeSearchTerm(string $value): string
    {
        $normalized = strtolower(trim($value));
        $normalized = str_replace([',', '.', '-', '/'], ' ', $normalized);
        $normalized = preg_replace('/\s+/', ' ', $normalized);

        return trim((string) $normalized);
    }

    private function extractSearchYear(string $search): ?int
    {
        if (preg_match('/\b(19|20)\d{2}\b/', $search, $matches) === 1) {
            return (int) ($matches[0] ?? 0);
        }

        return null;
    }

    private function formatCoordinateLocation($latitude, $longitude): ?string
    {
        if ($latitude === null || $longitude === null || $latitude === '' || $longitude === '') {
            return null;
        }

        $lat = (float) $latitude;
        $lng = (float) $longitude;

        return sprintf('%.5f, %.5f', $lat, $lng);
    }

    public function updateDayRecord(Request $request, string $date, EmployeeTest $employee)
    {
        $validated = $request->validate([
            'time_in' => ['nullable', 'regex:/^\d{2}:\d{2}(:\d{2})?$/'],
            'time_out' => ['nullable', 'regex:/^\d{2}:\d{2}(:\d{2})?$/']
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

        EmployeeAttendance::updateOrCreate(
            [
                'employee_id' => $employee->id,
                'attendance_date' => $date
            ],
            [
                'time_in' => $timeIn,
                'time_out' => $timeOut,
                'payroll_start' => $lastAttendance?->payroll_start?->toDateString() ?? $date,
                'payroll_end' => $lastAttendance?->payroll_end?->toDateString() ?? $date
            ]
        );

        return response()->json(['status' => 'ok']);
    }
}
