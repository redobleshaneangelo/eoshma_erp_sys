<?php

namespace App\Http\Controllers;

use App\Models\EmployeeAttendance;
use App\Models\EmployeeTest;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TimekeepingController extends Controller
{
    public function days(Request $request)
    {
        $month = $request->query('month');
        $year = $request->query('year');
        $status = $request->query('status');
        $search = trim((string) $request->query('search', ''));

        $query = EmployeeAttendance::query()
            ->select(
                'attendance_date',
                DB::raw('COUNT(*) as record_count'),
                DB::raw("SUM(CASE WHEN time_in IS NULL AND time_out IS NULL THEN 1 ELSE 0 END) as awol_count"),
                DB::raw("SUM(CASE WHEN time_in IS NOT NULL AND time_in > '08:05:00' THEN 1 ELSE 0 END) as late_count"),
                DB::raw("SUM(CASE WHEN time_in IS NOT NULL AND time_in <= '08:05:00' THEN 1 ELSE 0 END) as present_count")
            )
            ->when($month, function ($q) use ($month) {
                $q->whereMonth('attendance_date', $month);
            })
            ->when($year, function ($q) use ($year) {
                $q->whereYear('attendance_date', $year);
            })
            ->when($search !== '', function ($q) use ($search, $year) {
                $parsed = null;
                try {
                    $parsed = Carbon::parse($search);
                    if ($year) {
                        $parsed->year((int) $year);
                    }
                } catch (\Exception $e) {
                    $parsed = null;
                }

                if ($parsed) {
                    $q->whereDate('attendance_date', $parsed->toDateString());
                    return;
                }

                $q->where('attendance_date', 'like', '%' . $search . '%');
            })
            ->groupBy('attendance_date')
            ->orderBy('attendance_date', 'desc');

        $rows = $query->get()->map(function ($row) {
            $status = 'present';
            if ((int) $row->record_count === (int) $row->awol_count) {
                $status = 'awol';
            } elseif ((int) $row->late_count > 0) {
                $status = 'late';
            }

            $date = Carbon::parse($row->attendance_date);

            return [
                'date' => $date->toDateString(),
                'dayOfWeek' => $date->format('l'),
                'status' => $status,
                'recordCount' => (int) $row->record_count,
                'presentCount' => (int) $row->present_count,
                'lateCount' => (int) $row->late_count,
                'awolCount' => (int) $row->awol_count
            ];
        });

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
        $status = $request->query('status');
        $search = $request->query('search');

        $statusCase = "CASE WHEN ea.time_in IS NULL AND ea.time_out IS NULL THEN 'awol' WHEN ea.time_in > '08:05:00' THEN 'late' ELSE 'present' END";

        $nameExpression = "TRIM(CONCAT(users.last_name, ', ', users.first_name, IF(users.middle_name IS NULL OR users.middle_name = '', '', CONCAT(' ', LEFT(users.middle_name, 1), '.'))))";

        $baseQuery = EmployeeTest::query()
            ->leftJoin('users', 'employees.user_id', '=', 'users.id')
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
                DB::raw("{$statusCase} as status")
            )
            ->orderByRaw("{$nameExpression} asc");

        if ($search) {
            $baseQuery->where(function ($q) use ($search, $nameExpression) {
                $q->whereRaw("{$nameExpression} like ?", ['%' . $search . '%'])
                    ->orWhere('employees.id', 'like', '%' . $search . '%');
            });
        }

        if ($status) {
            $baseQuery->whereRaw("{$statusCase} = ?", [$status]);
        }

        $paginator = $baseQuery->paginate($perPage, ['*'], 'page', $page);
        $items = collect($paginator->items())->map(function ($item) {
            return [
                'id' => $item->id,
                'name' => $item->name,
                'position' => $item->position,
                'timeIn' => $item->time_in,
                'timeOut' => $item->time_out,
                'status' => $item->status
            ];
        });

        $summaryRows = EmployeeTest::query()
            ->leftJoin('employee_attendances as ea', function ($join) use ($date) {
                $join->on('employees.id', '=', 'ea.employee_id')
                    ->whereDate('ea.attendance_date', $date);
            })
            ->select(DB::raw("{$statusCase} as status"), DB::raw('COUNT(*) as total'))
            ->groupBy('status')
            ->get()
            ->pluck('total', 'status');

        $dateValue = Carbon::parse($date);

        return response()->json([
            'data' => $items,
            'meta' => [
                'current_page' => $paginator->currentPage(),
                'last_page' => $paginator->lastPage(),
                'per_page' => $paginator->perPage(),
                'total' => $paginator->total()
            ],
            'summary' => [
                'date' => $dateValue->toDateString(),
                'dayOfWeek' => $dateValue->format('l'),
                'presentCount' => (int) ($summaryRows['present'] ?? 0),
                'lateCount' => (int) ($summaryRows['late'] ?? 0),
                'awolCount' => (int) ($summaryRows['awol'] ?? 0)
            ]
        ]);
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
                'payroll_end' => $lastAttendance?->payroll_end?->toDateString() ?? $date,
                'payroll_type' => $lastAttendance?->payroll_type ?? 'hour',
                'payroll_frequency' => $lastAttendance?->payroll_frequency ?? 'Weekly'
            ]
        );

        return response()->json(['status' => 'ok']);
    }
}
