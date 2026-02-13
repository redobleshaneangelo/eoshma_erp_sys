<?php

namespace App\Http\Controllers;

use App\Models\PayrollGroup;
use App\Models\EmployeeTest;
use Illuminate\Http\Request;

class PayrollGroupController extends Controller
{
    public function index()
    {
        $groups = PayrollGroup::query()
            ->withCount('employees')
            ->orderBy('name')
            ->get()
            ->map(function ($group) {
                return [
                    'id' => $group->id,
                    'name' => $group->name,
                    'payrollFrequency' => $group->payroll_frequency,
                    'members' => $group->employees_count
                ];
            });

        return response()->json(['data' => $groups]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'payroll_frequency' => ['required', 'in:Monthly,Semi-Monthly,Weekly,Bi-Weekly']
        ]);

        $group = PayrollGroup::create([
            'name' => $validated['name'],
            'payroll_frequency' => $validated['payroll_frequency']
        ]);

        return response()->json([
            'data' => [
                'id' => $group->id,
                'name' => $group->name,
                'payrollFrequency' => $group->payroll_frequency,
                'members' => 0
            ]
        ], 201);
    }

    public function members(PayrollGroup $payrollGroup)
    {
        $rows = EmployeeTest::query()
            ->with('user')
            ->where('payroll_frequency', $payrollGroup->payroll_frequency)
            ->orderBy('id')
            ->get()
            ->map(function ($employee) use ($payrollGroup) {
                return [
                    'id' => $employee->id,
                    'name' => $employee->name,
                    'position' => $employee->position,
                    'payrollFrequency' => $employee->payroll_frequency,
                    'inGroup' => $employee->payroll_group_id === $payrollGroup->id
                ];
            });

        return response()->json(['data' => $rows]);
    }

    public function addMember(Request $request, PayrollGroup $payrollGroup)
    {
        $validated = $request->validate([
            'employee_id' => ['required', 'exists:employees,id']
        ]);

        $employee = EmployeeTest::findOrFail($validated['employee_id']);
        if ($employee->payroll_frequency !== $payrollGroup->payroll_frequency) {
            return response()->json([
                'message' => 'Employee payroll frequency does not match this group.'
            ], 422);
        }

        $employee->update(['payroll_group_id' => $payrollGroup->id]);

        return response()->json(['status' => 'ok']);
    }

    public function removeMember(PayrollGroup $payrollGroup, EmployeeTest $employee)
    {
        if ($employee->payroll_group_id !== $payrollGroup->id) {
            return response()->json(['message' => 'Employee is not in this group.'], 409);
        }

        $employee->update(['payroll_group_id' => null]);

        return response()->json(['status' => 'ok']);
    }
}
