<?php

namespace App\Http\Controllers;

use App\Models\EmployeePayslip;
use App\Models\EmployeeTest;
use Illuminate\Http\Request;

class EmployeePayslipController extends Controller
{
    public function showMine(Request $request, EmployeePayslip $employeePayslip)
    {
        $employee = EmployeeTest::where('user_id', $request->user()->id)->first();

        if (!$employee || $employeePayslip->employee_id !== $employee->id) {
            return response()->json(['message' => 'Payslip not found.'], 404);
        }

        $employeePayslip->load(['payrollRun', 'employee.user']);

        return response()->json([
            'data' => [
                'id' => $employeePayslip->id,
                'periodStart' => optional($employeePayslip->period_start)->toDateString(),
                'periodEnd' => optional($employeePayslip->period_end)->toDateString(),
                'payDate' => optional($employeePayslip->pay_date)->toDateString(),
                'payrollName' => $employeePayslip->payrollRun?->name,
                'employeeName' => $employeePayslip->employee?->name,
                'position' => $employeePayslip->employee?->position,
                'employeeCode' => $employeePayslip->employee_id,
                'earnings' => [
                    'baseSalary' => (float) $employeePayslip->basic_pay,
                    'overtimePay' => (float) $employeePayslip->overtime_pay,
                    'bonuses' => (float) $employeePayslip->allowances,
                    'grossSalary' => (float) $employeePayslip->gross_pay
                ],
                'deductions' => [
                    'taxes' => (float) $employeePayslip->taxes,
                    'latePenalties' => 0,
                    'absences' => 0,
                    'otherDeductions' => (float) $employeePayslip->other_deductions,
                    'total' => (float) $employeePayslip->total_deductions
                ],
                'netPay' => (float) $employeePayslip->net_pay,
                'releasedAt' => optional($employeePayslip->released_at)?->toDateTimeString()
            ]
        ]);
    }
}
