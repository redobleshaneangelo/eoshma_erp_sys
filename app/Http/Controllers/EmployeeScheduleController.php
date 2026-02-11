<?php

namespace App\Http\Controllers;

use App\Models\EmployeeSchedule;
use Illuminate\Http\Request;

class EmployeeScheduleController extends Controller
{
    public function index(Request $request)
    {
        $query = EmployeeSchedule::query();

        if ($request->filled('employee_id')) {
            $query->where('employee_id', $request->query('employee_id'));
        }

        return response()->json([
            'data' => $query->orderBy('employee_id')->get()
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'employee_id' => ['required', 'integer', 'exists:employees,id'],
            'start_time' => ['required', 'regex:/^\d{2}:\d{2}(:\d{2})?$/'],
            'end_time' => ['required', 'regex:/^\d{2}:\d{2}(:\d{2})?$/']
        ]);

        $schedule = EmployeeSchedule::updateOrCreate(
            [
                'employee_id' => $validated['employee_id']
            ],
            [
                'start_time' => $validated['start_time'],
                'end_time' => $validated['end_time']
            ]
        );

        return response()->json(['data' => $schedule], 201);
    }
}
