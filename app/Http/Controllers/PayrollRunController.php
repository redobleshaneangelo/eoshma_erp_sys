<?php

namespace App\Http\Controllers;

use App\Models\EmployeeAttendance;
use App\Models\EmployeeTest;
use App\Models\Allowance;
use App\Models\Deduction;
use App\Models\PayrollRun;
use App\Models\PayrollRunEligibility;
use App\Models\StatutoryCompliance;
use App\Models\OvertimeRate;
use App\Models\OvertimeRequest;
use App\Models\PayrollGroup;
use App\Models\UsersSalaryType;
use App\Models\ChartAccount;
use App\Models\ChartAccountTransaction;
use App\Models\EmployeePayslip;
use App\Notifications\PayslipReleasedNotification;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PayrollRunController extends Controller
{
    public function index()
    {
        $runs = PayrollRun::query()
            ->orderByDesc('created_at')
            ->get();

        return response()->json([
            'data' => $runs
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'start_date' => ['required', 'date'],
            'end_date' => ['required', 'date'],
            'payroll_group_id' => ['required', 'exists:payroll_groups,id'],
            'status' => ['nullable', 'string', 'max:50'],
            'pay_date' => ['required', 'date'],
            'description' => ['nullable', 'string', 'max:1000']
        ]);

        if (empty($validated['status'])) {
            $validated['status'] = 'Draft';
        }

        $payrollGroup = PayrollGroup::find($validated['payroll_group_id']);
        $validated['group'] = $payrollGroup?->name ?? 'Payroll Group';
        $validated['payroll_group_id'] = $payrollGroup?->id;
        $validated['frequency'] = $payrollGroup?->payroll_frequency ?? 'Monthly';

        $payrollRun = PayrollRun::create($validated);

        return response()->json([
            'data' => $payrollRun
        ], 201);
    }

    public function show(PayrollRun $payrollRun)
    {
        return response()->json([
            'data' => $payrollRun
        ]);
    }

    public function attendanceSummary(PayrollRun $payrollRun)
    {
        $rows = $this->attendanceRows($payrollRun);
        $eligibility = PayrollRunEligibility::query()
            ->where('payroll_run_id', $payrollRun->id)
            ->pluck('is_eligible', 'employee_id');

        $overtimeRates = OvertimeRate::query()->get()->keyBy('day_type');
        $overtimeRequests = OvertimeRequest::query()
            ->where('status', 'Approved')
            ->whereBetween('request_date', [$payrollRun->start_date, $payrollRun->end_date])
            ->get()
            ->groupBy('employee_id');

        $grouped = $rows->groupBy('employee_id')->map(function ($items) use ($payrollRun, $eligibility, $overtimeRequests, $overtimeRates) {
            $employee = $items->first()->employee;
            $totalMinutes = $this->totalMinutes($items);
            $employeeOvertime = $overtimeRequests->get($employee->id, collect());
            $hourlyRate = $employee->hourly_rate ?? $employee->rate;
            $overtimeHours = $employeeOvertime->sum(function ($item) {
                return $item->approved_hours ?? $item->hours;
            });
            $overtimePay = $employeeOvertime->sum(function ($item) use ($hourlyRate, $overtimeRates) {
                $hours = $item->approved_hours ?? $item->hours;
                $multiplier = $item->approved_multiplier
                    ?? $overtimeRates->get($item->day_type)?->multiplier
                    ?? 0;
                return $hours * $hourlyRate * $multiplier;
            });

            return [
                'id' => $employee->id,
                'name' => $employee->name,
                'role' => $employee->position,
                'rate' => $employee->rate,
                'daysLogged' => $items->count(),
                'totalHours' => round($totalMinutes / 60, 2),
                'overtimeHours' => round($overtimeHours, 2),
                'overtimePay' => round($overtimePay, 2),
                'isEligible' => (bool) ($eligibility[$employee->id] ?? false),
                'records' => $items->map(function ($item) use ($payrollRun) {
                    return [
                        'date' => $item->attendance_date->toDateString(),
                        'timeIn' => $item->time_in,
                        'timeOut' => $item->time_out,
                        'payrollStart' => $payrollRun->start_date->toDateString(),
                        'payrollEnd' => $payrollRun->end_date->toDateString()
                    ];
                })->values(),
                'incidents' => []
            ];
        })->values();

        return response()->json([
            'data' => $grouped
        ]);
    }

    public function allowances(PayrollRun $payrollRun)
    {
        $rows = Allowance::query()
            ->with('employee')
            ->where('payroll_run_id', $payrollRun->id)
            ->orderByDesc('id')
            ->get()
            ->map(function ($item) {
                return [
                    'id' => $item->id,
                    'employeeId' => $item->employee_id,
                    'employeeName' => $item->employee?->name,
                    'type' => $item->type,
                    'amount' => (float) $item->amount,
                    'notes' => $item->notes
                ];
            });

        return response()->json(['data' => $rows]);
    }

    public function storeAllowance(Request $request, PayrollRun $payrollRun)
    {
        $validated = $request->validate([
            'employee_id' => ['required', 'exists:employees,id'],
            'type' => ['required', 'string', 'max:255'],
            'amount' => ['required', 'numeric', 'min:0'],
            'notes' => ['nullable', 'string', 'max:255']
        ]);

        $validated['payroll_run_id'] = $payrollRun->id;
        $allowance = Allowance::create($validated)->load('employee');

        return response()->json([
            'data' => [
                'id' => $allowance->id,
                'employeeId' => $allowance->employee_id,
                'employeeName' => $allowance->employee?->name,
                'type' => $allowance->type,
                'amount' => (float) $allowance->amount,
                'notes' => $allowance->notes
            ]
        ], 201);
    }

    public function updateAllowance(Request $request, PayrollRun $payrollRun, Allowance $allowance)
    {
        if ($allowance->payroll_run_id !== $payrollRun->id) {
            abort(404);
        }

        $validated = $request->validate([
            'type' => ['required', 'string', 'max:255'],
            'amount' => ['required', 'numeric', 'min:0'],
            'notes' => ['nullable', 'string', 'max:255']
        ]);

        $allowance->update($validated);

        return response()->json([
            'data' => [
                'id' => $allowance->id,
                'employeeId' => $allowance->employee_id,
                'employeeName' => $allowance->employee?->name,
                'type' => $allowance->type,
                'amount' => (float) $allowance->amount,
                'notes' => $allowance->notes
            ]
        ]);
    }

    public function deleteAllowance(PayrollRun $payrollRun, Allowance $allowance)
    {
        if ($allowance->payroll_run_id !== $payrollRun->id) {
            abort(404);
        }

        $allowance->delete();

        return response()->json(['status' => 'ok']);
    }

    public function deductions(PayrollRun $payrollRun)
    {
        $rows = Deduction::query()
            ->with('employee')
            ->where('payroll_run_id', $payrollRun->id)
            ->orderByDesc('id')
            ->get()
            ->map(function ($item) {
                return [
                    'id' => $item->id,
                    'employeeId' => $item->employee_id,
                    'employeeName' => $item->employee?->name,
                    'type' => $item->type,
                    'amount' => (float) $item->amount,
                    'notes' => $item->notes
                ];
            });

        return response()->json(['data' => $rows]);
    }

    public function storeDeduction(Request $request, PayrollRun $payrollRun)
    {
        $validated = $request->validate([
            'employee_id' => ['required', 'exists:employees,id'],
            'type' => ['required', 'string', 'max:255'],
            'amount' => ['required', 'numeric', 'min:0'],
            'notes' => ['nullable', 'string', 'max:255']
        ]);

        $validated['payroll_run_id'] = $payrollRun->id;
        $deduction = Deduction::create($validated)->load('employee');

        return response()->json([
            'data' => [
                'id' => $deduction->id,
                'employeeId' => $deduction->employee_id,
                'employeeName' => $deduction->employee?->name,
                'type' => $deduction->type,
                'amount' => (float) $deduction->amount,
                'notes' => $deduction->notes
            ]
        ], 201);
    }

    public function updateDeduction(Request $request, PayrollRun $payrollRun, Deduction $deduction)
    {
        if ($deduction->payroll_run_id !== $payrollRun->id) {
            abort(404);
        }

        $validated = $request->validate([
            'type' => ['required', 'string', 'max:255'],
            'amount' => ['required', 'numeric', 'min:0'],
            'notes' => ['nullable', 'string', 'max:255']
        ]);

        $deduction->update($validated);

        return response()->json([
            'data' => [
                'id' => $deduction->id,
                'employeeId' => $deduction->employee_id,
                'employeeName' => $deduction->employee?->name,
                'type' => $deduction->type,
                'amount' => (float) $deduction->amount,
                'notes' => $deduction->notes
            ]
        ]);
    }

    public function deleteDeduction(PayrollRun $payrollRun, Deduction $deduction)
    {
        if ($deduction->payroll_run_id !== $payrollRun->id) {
            abort(404);
        }

        $deduction->delete();

        return response()->json(['status' => 'ok']);
    }

    public function statutoryCompliance(Request $request, PayrollRun $payrollRun)
    {
        $basis = $request->query('basis', 'monthly');
        $computed = $this->buildComputedRows($payrollRun);
        $totalGross = $computed['totalGross'];
        $totalTax = $computed['totalTax'];

        $periodsPerYear = $this->periodsPerYear($payrollRun->frequency);
        $monthlyBase = $periodsPerYear > 0 ? ($totalGross * ($periodsPerYear / 12)) : $totalGross;
        $base = $basis === 'period' ? $totalGross : $monthlyBase;
        $philHealthBase = min($base, 100000);

        $sssEmployee = round($base * 0.045, 2);
        $sssEmployer = round($base * 0.095, 2);
        $philHealthEmployee = round($philHealthBase * 0.025, 2);
        $philHealthEmployer = round($philHealthBase * 0.025, 2);
        $pagibigEmployee = round(min($base * 0.02, 100), 2);
        $pagibigEmployer = round(min($base * 0.02, 100), 2);
        $dueDate = Carbon::parse($payrollRun->end_date)->addMonth()->toDateString();

        $rows = collect([
            [
                'name' => 'SSS Contribution',
                'employeeShare' => $sssEmployee,
                'employerShare' => $sssEmployer
            ],
            [
                'name' => 'PhilHealth Premium',
                'employeeShare' => $philHealthEmployee,
                'employerShare' => $philHealthEmployer
            ],
            [
                'name' => 'Pag-IBIG Fund',
                'employeeShare' => $pagibigEmployee,
                'employerShare' => $pagibigEmployer
            ],
            [
                'name' => 'Withholding Tax',
                'employeeShare' => round($totalTax, 2),
                'employerShare' => 0
            ]
        ])->map(function ($item, $index) use ($payrollRun, $dueDate) {
            return [
                'id' => $index + 1,
                'name' => $item['name'],
                'coverage' => $payrollRun->start_date->toDateString() . ' - ' . $payrollRun->end_date->toDateString(),
                'employeeShare' => $item['employeeShare'],
                'employerShare' => $item['employerShare'],
                'total' => $item['employeeShare'] + $item['employerShare'],
                'dueDate' => $dueDate
            ];
        });

        $perEmployeeRows = [];
        $rowId = 1;
        foreach ($computed['rows'] as $employeeRow) {
            $gross = (float) ($employeeRow['grossPay'] ?? 0);
            $monthlyBase = $periodsPerYear > 0 ? ($gross * ($periodsPerYear / 12)) : $gross;
            $base = $basis === 'period' ? $gross : $monthlyBase;
            $philHealthBase = min($base, 100000);

            $employeeRows = [
                [
                    'name' => 'SSS Contribution',
                    'employeeShare' => round($base * 0.045, 2),
                    'employerShare' => round($base * 0.095, 2)
                ],
                [
                    'name' => 'PhilHealth Premium',
                    'employeeShare' => round($philHealthBase * 0.025, 2),
                    'employerShare' => round($philHealthBase * 0.025, 2)
                ],
                [
                    'name' => 'Pag-IBIG Fund',
                    'employeeShare' => round(min($base * 0.02, 100), 2),
                    'employerShare' => round(min($base * 0.02, 100), 2)
                ],
                [
                    'name' => 'Withholding Tax',
                    'employeeShare' => round((float) ($employeeRow['tax'] ?? 0), 2),
                    'employerShare' => 0
                ]
            ];

            foreach ($employeeRows as $row) {
                $perEmployeeRows[] = [
                    'id' => $rowId++,
                    'employeeId' => $employeeRow['id'] ?? null,
                    'employeeName' => $employeeRow['employee'] ?? '--',
                    'role' => $employeeRow['role'] ?? '--',
                    'name' => $row['name'],
                    'coverage' => $payrollRun->start_date->toDateString() . ' - ' . $payrollRun->end_date->toDateString(),
                    'employeeShare' => $row['employeeShare'],
                    'employerShare' => $row['employerShare'],
                    'total' => $row['employeeShare'] + $row['employerShare'],
                    'dueDate' => $dueDate
                ];
            }
        }

        return response()->json([
            'data' => $rows,
            'perEmployee' => $perEmployeeRows
        ]);
    }

    public function computedPayroll(PayrollRun $payrollRun)
    {
        $computed = $this->buildComputedRows($payrollRun);

        return response()->json(['data' => $computed['rows']]);
    }

    public function updateEligibility(Request $request, PayrollRun $payrollRun)
    {
        $validated = $request->validate([
            'employee_id' => ['required', 'exists:employees,id'],
            'is_eligible' => ['required', 'boolean']
        ]);

        $eligibility = PayrollRunEligibility::updateOrCreate(
            [
                'payroll_run_id' => $payrollRun->id,
                'employee_id' => $validated['employee_id']
            ],
            ['is_eligible' => $validated['is_eligible']]
        );

        return response()->json([
            'data' => [
                'employeeId' => $eligibility->employee_id,
                'isEligible' => (bool) $eligibility->is_eligible
            ]
        ]);
    }

    public function bulkEligibility(Request $request, PayrollRun $payrollRun)
    {
        $validated = $request->validate([
            'employee_ids' => ['required', 'array'],
            'employee_ids.*' => ['integer', 'exists:employees,id'],
            'is_eligible' => ['required', 'boolean']
        ]);

        foreach ($validated['employee_ids'] as $employeeId) {
            PayrollRunEligibility::updateOrCreate(
                [
                    'payroll_run_id' => $payrollRun->id,
                    'employee_id' => $employeeId
                ],
                ['is_eligible' => $validated['is_eligible']]
            );
        }

        return response()->json(['status' => 'ok']);
    }

    public function sendForApproval(PayrollRun $payrollRun)
    {
        if ($payrollRun->status === 'Pending') {
            return response()->json([
                'message' => 'This payroll run is already pending HR approval.'
            ], 422);
        }

        if (in_array($payrollRun->status, ['Pending Finance Approval', 'Approved', 'Completed'], true)) {
            return response()->json([
                'message' => 'This payroll run cannot be sent to HR pending at its current status.'
            ], 422);
        }

        if (!in_array($payrollRun->status, ['Draft', 'Rejected'], true)) {
            return response()->json([
                'message' => 'Only draft or rejected payroll runs can be sent for HR approval.'
            ], 422);
        }

        $payrollRun->update([
            'status' => 'Pending',
            'reject_reason' => null
        ]);

        return response()->json([
            'data' => $payrollRun
        ]);
    }

    public function sendToFinanceApproval(PayrollRun $payrollRun)
    {
        if ($payrollRun->status !== 'Pending') {
            return response()->json([
                'message' => 'Only payroll runs with Pending status can be sent to Finance approval.'
            ], 422);
        }

        $eligibleCount = PayrollRunEligibility::query()
            ->where('payroll_run_id', $payrollRun->id)
            ->where('is_eligible', true)
            ->count();

        if ($eligibleCount < 1) {
            return response()->json([
                'message' => 'Cannot proceed: at least one employee must be marked eligible.'
            ], 422);
        }

        $breakdown = $this->buildPayrollPostingBreakdown($payrollRun);

        if ($breakdown['totalCredits'] <= 0) {
            return response()->json([
                'message' => 'Cannot send for approval because computed payroll amount is zero.'
            ], 422);
        }

        try {
            DB::transaction(function () use ($payrollRun, $breakdown) {
                $this->clearPayrollPosting($payrollRun->id);
                $this->postPayrollToLedger($payrollRun, $breakdown);
                $payrollRun->update([
                    'status' => 'Pending Finance Approval',
                    'reject_reason' => null
                ]);
            });
        } catch (\RuntimeException $exception) {
            return response()->json([
                'message' => $exception->getMessage()
            ], 422);
        }

        return response()->json([
            'data' => $payrollRun
        ]);
    }

    public function approve(PayrollRun $payrollRun)
    {
        if ($payrollRun->status !== 'Pending Finance Approval') {
            return response()->json([
                'message' => 'Only payroll runs pending Finance approval can be approved.'
            ], 422);
        }

        $payrollRun->update(['status' => 'Approved']);

        return response()->json([
            'data' => $payrollRun
        ]);
    }

    public function reject(Request $request, PayrollRun $payrollRun)
    {
        if (!in_array($payrollRun->status, ['Pending Finance Approval', 'Pending'], true)) {
            return response()->json([
                'message' => 'Only pending payroll runs can be rejected.'
            ], 422);
        }

        $validated = $request->validate([
            'reason' => ['required', 'string', 'max:1000']
        ]);

        $this->clearPayrollPosting($payrollRun->id);
        $payrollRun->update([
            'status' => 'Rejected',
            'reject_reason' => $validated['reason']
        ]);

        return response()->json([
            'data' => $payrollRun
        ]);
    }

    public function releasePayslip(PayrollRun $payrollRun)
    {
        if ($payrollRun->status === 'Completed') {
            return response()->json([
                'message' => 'This payroll run has already been released.'
            ], 422);
        }

        if ($payrollRun->status !== 'Approved') {
            return response()->json([
                'message' => 'Only approved payroll runs can release payslips.'
            ], 422);
        }

        $breakdown = $this->buildPayrollPostingBreakdown($payrollRun);
        $computed = $this->buildComputedRows($payrollRun);

        try {
            DB::transaction(function () use ($payrollRun, $breakdown, $computed) {
                $this->postPayrollSettlementOnRelease($payrollRun, $breakdown);
                $this->generatePayslipsAndNotifyEmployees($payrollRun, $computed['rows']);
                $payrollRun->update(['status' => 'Completed']);
            });
        } catch (\RuntimeException $exception) {
            return response()->json([
                'message' => $exception->getMessage()
            ], 422);
        }

        return response()->json([
            'data' => $payrollRun
        ]);
    }

    private function buildComputedRows(PayrollRun $payrollRun): array
    {
        $rows = $this->attendanceRows($payrollRun);
        $grouped = $rows->toBase()->groupBy('employee_id');
        $eligibleIds = PayrollRunEligibility::query()
            ->where('payroll_run_id', $payrollRun->id)
            ->where('is_eligible', true)
            ->pluck('employee_id')
            ->map(fn ($id) => (int) $id)
            ->all();

        if ($this->shouldFilterEligible($payrollRun)) {
            $grouped = $grouped->only($eligibleIds);
        }

        $allowances = Allowance::query()
            ->where('payroll_run_id', $payrollRun->id)
            ->get()
            ->groupBy('employee_id');

        $deductions = Deduction::query()
            ->where('payroll_run_id', $payrollRun->id)
            ->get()
            ->groupBy('employee_id');

        $overtimeRates = OvertimeRate::query()->get()->keyBy('day_type');
        $overtimeRequests = OvertimeRequest::query()
            ->where('status', 'Approved')
            ->whereBetween('request_date', [$payrollRun->start_date, $payrollRun->end_date])
            ->get()
            ->groupBy('employee_id');

        $periodsPerYear = $this->periodsPerYear($payrollRun->frequency);
        $totalGross = 0;
        $totalTax = 0;

        $computed = $grouped->map(function ($items, $employeeId) use ($payrollRun, $allowances, $deductions, $overtimeRequests, $overtimeRates, $periodsPerYear, &$totalGross, &$totalTax) {
            $employee = $items->first()->employee ?? EmployeeTest::find($employeeId);
            if (!$employee) {
                return null;
            }

            $totalMinutes = $this->totalMinutes($items);
            $totalHours = round($totalMinutes / 60, 2);
            $daysLogged = $items->count();

            $employee->loadMissing('user.salaryType');
            $salaryType = $employee->user?->salaryType?->salary_type ?? 'hour';
            $basicPay = $this->basicPay($salaryType, $employee->rate, $daysLogged, $totalHours);
            $allowanceTotal = $allowances->get($employeeId, collect())->sum('amount');
            $deductionTotal = $deductions->get($employeeId, collect())->sum('amount');

            $employeeOvertime = $overtimeRequests->get($employeeId, collect());
            $hourlyRate = $employee->hourly_rate ?? $employee->rate;
            $overtimeHours = $employeeOvertime->sum(function ($item) {
                return $item->approved_hours ?? $item->hours;
            });
            $overtimePay = $employeeOvertime->sum(function ($item) use ($hourlyRate, $overtimeRates) {
                $hours = $item->approved_hours ?? $item->hours;
                $multiplier = $item->approved_multiplier
                    ?? $overtimeRates->get($item->day_type)?->multiplier
                    ?? 0;
                return $hours * $hourlyRate * $multiplier;
            });

            $grossPay = $basicPay + $allowanceTotal + $overtimePay - $deductionTotal;
            $taxableAnnual = $grossPay * $periodsPerYear;
            $annualTax = $this->calculateAnnualTax($taxableAnnual);
            $tax = $periodsPerYear > 0 ? round($annualTax / $periodsPerYear, 2) : 0;
            $netPay = $grossPay - $tax;

            $totalGross += $grossPay;
            $totalTax += $tax;

            return [
                'id' => $employee->id,
                'employee' => $employee->name,
                'role' => $employee->position,
                'basicPay' => round($basicPay, 2),
                'allowance' => round($allowanceTotal, 2),
                'overtimeHours' => round($overtimeHours, 2),
                'overtimePay' => round($overtimePay, 2),
                'grossPay' => round($grossPay, 2),
                'deductions' => round($deductionTotal + $tax, 2),
                'tax' => round($tax, 2),
                'netPay' => round($netPay, 2)
            ];
        })->filter()->values();

        return [
            'rows' => $computed,
            'totalGross' => round($totalGross, 2),
            'totalTax' => round($totalTax, 2)
        ];
    }

    private function buildPayrollPostingBreakdown(PayrollRun $payrollRun): array
    {
        $computed = $this->buildComputedRows($payrollRun);
        $eligibleIds = PayrollRunEligibility::query()
            ->where('payroll_run_id', $payrollRun->id)
            ->where('is_eligible', true)
            ->pluck('employee_id')
            ->map(fn ($id) => (int) $id)
            ->all();

        $rows = collect($computed['rows'])
            ->filter(fn ($row) => in_array((int) ($row['id'] ?? 0), $eligibleIds, true))
            ->values();

        $netPay = round((float) $rows->sum(fn ($row) => (float) ($row['netPay'] ?? 0)), 2);
        $withholdingTax = round((float) $rows->sum(fn ($row) => (float) ($row['tax'] ?? 0)), 2);

        $sssEmployee = 0;
        $sssEmployer = 0;
        $philEmployee = 0;
        $philEmployer = 0;
        $pagibigEmployee = 0;
        $pagibigEmployer = 0;

        foreach ($rows as $row) {
            $gross = (float) ($row['grossPay'] ?? 0);
            $base = max($gross, 0);
            $philBase = min(max($gross, 10000), 100000);

            $sssEmployee += round($base * 0.045, 2);
            $sssEmployer += round($base * 0.095, 2);
            $philEmployee += round($philBase * 0.025, 2);
            $philEmployer += round($philBase * 0.025, 2);
            $pagibigEmployee += round(min($base * 0.02, 100), 2);
            $pagibigEmployer += round(min($base * 0.02, 100), 2);
        }

        $sssPayable = round($sssEmployee + $sssEmployer, 2);
        $philPayable = round($philEmployee + $philEmployer, 2);
        $pagibigPayable = round($pagibigEmployee + $pagibigEmployer, 2);
        $employeeContrib = round($sssEmployee + $philEmployee + $pagibigEmployee, 2);
        $employerContrib = round($sssEmployer + $philEmployer + $pagibigEmployer, 2);

        $salaryExpenseDebit = round($netPay + $withholdingTax + $employeeContrib, 2);
        $employerExpenseDebit = round($employerContrib, 2);

        $totalDebits = round($salaryExpenseDebit + $employerExpenseDebit, 2);
        $totalCredits = round($netPay + $withholdingTax + $sssPayable + $philPayable + $pagibigPayable, 2);

        return [
            'salaryExpenseDebit' => $salaryExpenseDebit,
            'employerExpenseDebit' => $employerExpenseDebit,
            'payrollPayable' => round($netPay, 2),
            'withholdingTaxPayable' => round($withholdingTax, 2),
            'sssPayable' => $sssPayable,
            'philPayable' => $philPayable,
            'pagibigPayable' => $pagibigPayable,
            'totalDebits' => $totalDebits,
            'totalCredits' => $totalCredits,
        ];
    }

    private function postPayrollToLedger(PayrollRun $payrollRun, array $breakdown): void
    {
        $payrollPayable = ChartAccount::query()->where('code', '2200')->first();
        $withholdingPayable = ChartAccount::query()->where('code', '2210')->first();
        $sssPayable = ChartAccount::query()->where('code', '2220')->first();
        $philPayable = ChartAccount::query()->where('code', '2230')->first();
        $pagibigPayable = ChartAccount::query()->where('code', '2240')->first();

        $salaryExpense = ChartAccount::query()->where('code', '6100')->first();
        $employerContributionExpense = ChartAccount::query()->where('code', '6110')->first();

        if (
            !$payrollPayable || !$withholdingPayable || !$sssPayable || !$philPayable || !$pagibigPayable
            || !$salaryExpense || !$employerContributionExpense
        ) {
            throw new \RuntimeException('Missing required finance chart account codes 2200, 2210, 2220, 2230, 2240, 6100, or 6110.');
        }

        if (round((float) $breakdown['totalDebits'], 2) !== round((float) $breakdown['totalCredits'], 2)) {
            throw new \RuntimeException('Unbalanced payroll posting detected. Debits and credits must be equal.');
        }

        $reference = 'PR-' . $payrollRun->id;
        $description = 'Payroll submission for finance approval';

        if ((float) $breakdown['salaryExpenseDebit'] > 0) {
            $this->appendTransaction($salaryExpense, $payrollRun->id, $payrollRun->pay_date, $description, $reference, (float) $breakdown['salaryExpenseDebit'], 0);
        }

        if ((float) $breakdown['employerExpenseDebit'] > 0) {
            $this->appendTransaction($employerContributionExpense, $payrollRun->id, $payrollRun->pay_date, $description, $reference, (float) $breakdown['employerExpenseDebit'], 0);
        }

        if ((float) $breakdown['payrollPayable'] > 0) {
            $this->appendTransaction($payrollPayable, $payrollRun->id, $payrollRun->pay_date, $description, $reference, 0, (float) $breakdown['payrollPayable']);
        }

        if ((float) $breakdown['withholdingTaxPayable'] > 0) {
            $this->appendTransaction($withholdingPayable, $payrollRun->id, $payrollRun->pay_date, $description, $reference, 0, (float) $breakdown['withholdingTaxPayable']);
        }

        if ((float) $breakdown['sssPayable'] > 0) {
            $this->appendTransaction($sssPayable, $payrollRun->id, $payrollRun->pay_date, $description, $reference, 0, (float) $breakdown['sssPayable']);
        }

        if ((float) $breakdown['philPayable'] > 0) {
            $this->appendTransaction($philPayable, $payrollRun->id, $payrollRun->pay_date, $description, $reference, 0, (float) $breakdown['philPayable']);
        }

        if ((float) $breakdown['pagibigPayable'] > 0) {
            $this->appendTransaction($pagibigPayable, $payrollRun->id, $payrollRun->pay_date, $description, $reference, 0, (float) $breakdown['pagibigPayable']);
        }
    }

    private function postPayrollSettlementOnRelease(PayrollRun $payrollRun, array $breakdown): void
    {
        $payrollPayable = ChartAccount::query()->where('code', '2200')->first();
        $cashInBank = ChartAccount::query()->where('code', '1110')->first();

        if (!$payrollPayable || !$cashInBank) {
            throw new \RuntimeException('Missing required finance chart account codes 2200 or 1110 for payroll settlement.');
        }

        $amount = round((float) ($breakdown['payrollPayable'] ?? 0), 2);
        if ($amount <= 0) {
            throw new \RuntimeException('Cannot release payslip because payroll payable amount is zero.');
        }

        $reference = 'PR-' . $payrollRun->id;
        $description = 'Payroll payslip release settlement';

        $this->appendTransaction($payrollPayable, $payrollRun->id, $payrollRun->pay_date, $description, $reference, $amount, 0);
        $this->appendTransaction($cashInBank, $payrollRun->id, $payrollRun->pay_date, $description, $reference, 0, $amount);
    }

    private function generatePayslipsAndNotifyEmployees(PayrollRun $payrollRun, $rows): void
    {
        foreach ($rows as $row) {
            $employeeId = (int) ($row['id'] ?? 0);
            if ($employeeId <= 0) {
                continue;
            }

            $otherDeductions = round(max(0, (float) ($row['deductions'] ?? 0) - (float) ($row['tax'] ?? 0)), 2);

            $payslip = EmployeePayslip::updateOrCreate(
                [
                    'employee_id' => $employeeId,
                    'payroll_run_id' => $payrollRun->id
                ],
                [
                    'period_start' => $payrollRun->start_date,
                    'period_end' => $payrollRun->end_date,
                    'pay_date' => $payrollRun->pay_date,
                    'basic_pay' => (float) ($row['basicPay'] ?? 0),
                    'overtime_pay' => (float) ($row['overtimePay'] ?? 0),
                    'allowances' => (float) ($row['allowance'] ?? 0),
                    'gross_pay' => (float) ($row['grossPay'] ?? 0),
                    'taxes' => (float) ($row['tax'] ?? 0),
                    'other_deductions' => $otherDeductions,
                    'total_deductions' => (float) ($row['deductions'] ?? 0),
                    'net_pay' => (float) ($row['netPay'] ?? 0),
                    'released_at' => now()
                ]
            );

            $employee = EmployeeTest::with('user')->find($employeeId);
            if (!$employee?->user) {
                continue;
            }

            $employee->user->notify(new PayslipReleasedNotification($payrollRun, $payslip));
        }
    }

    private function appendTransaction(ChartAccount $account, int $payrollRunId, $transactionDate, string $description, string $reference, float $debit, float $credit): void
    {
        $priorBalance = (float) $account->balance;
        $newBalance = round($priorBalance + $debit - $credit, 2);

        ChartAccountTransaction::create([
            'chart_account_id' => $account->id,
            'payroll_run_id' => $payrollRunId,
            'transaction_date' => $transactionDate,
            'description' => $description,
            'reference' => $reference,
            'debit' => round($debit, 2),
            'credit' => round($credit, 2),
            'balance' => $newBalance
        ]);

        $account->update([
            'total_debit' => round((float) $account->total_debit + $debit, 2),
            'total_credit' => round((float) $account->total_credit + $credit, 2),
            'balance' => $newBalance
        ]);
    }

    private function clearPayrollPosting(int $payrollRunId): void
    {
        $transactions = ChartAccountTransaction::query()
            ->where('payroll_run_id', $payrollRunId)
            ->get();

        if ($transactions->isEmpty()) {
            return;
        }

        foreach ($transactions as $transaction) {
            $account = ChartAccount::find($transaction->chart_account_id);
            if ($account) {
                $account->update([
                    'total_debit' => round(max(0, (float) $account->total_debit - (float) $transaction->debit), 2),
                    'total_credit' => round(max(0, (float) $account->total_credit - (float) $transaction->credit), 2),
                ]);
            }
        }

        ChartAccountTransaction::query()
            ->where('payroll_run_id', $payrollRunId)
            ->delete();

        $this->recomputeAllBalances();
    }

    private function recomputeAllBalances(): void
    {
        $accounts = ChartAccount::query()->orderBy('code')->get();

        foreach ($accounts as $account) {
            $running = 0;
            $accountTransactions = ChartAccountTransaction::query()
                ->where('chart_account_id', $account->id)
                ->orderBy('transaction_date')
                ->orderBy('id')
                ->get();

            foreach ($accountTransactions as $entry) {
                $running = round($running + (float) $entry->debit - (float) $entry->credit, 2);
                $entry->update(['balance' => $running]);
            }

            $account->update(['balance' => $running]);
        }
    }

    private function shouldFilterEligible(PayrollRun $payrollRun): bool
    {
        return in_array($payrollRun->status, ['Pending', 'Pending Finance Approval', 'Approved', 'Completed'], true);
    }

    private function attendanceRows(PayrollRun $payrollRun)
    {
        return EmployeeAttendance::query()
            ->with('employee')
            ->whereBetween('attendance_date', [$payrollRun->start_date, $payrollRun->end_date])
            ->when($payrollRun->payroll_group_id, function ($query) use ($payrollRun) {
                $query->whereHas('employee', function ($employeeQuery) use ($payrollRun) {
                    $employeeQuery->where('payroll_group_id', $payrollRun->payroll_group_id);
                });
            })
            ->when($payrollRun->frequency, function ($query) use ($payrollRun) {
                $query->whereHas('employee', function ($employeeQuery) use ($payrollRun) {
                    $employeeQuery->where('payroll_frequency', $payrollRun->frequency);
                });
            })
            ->orderBy('attendance_date')
            ->get();
    }

    private function totalMinutes($items): float
    {
        return $items->sum(function ($item) {
            if (!$item->time_in || !$item->time_out) {
                return 0;
            }
            $start = Carbon::createFromFormat('H:i:s', $item->time_in);
            $end = Carbon::createFromFormat('H:i:s', $item->time_out);
            return $end->diffInMinutes($start, true);
        });
    }

    private function basicPay(string $salaryType, float $rate, int $daysLogged, float $totalHours): float
    {
        return match ($salaryType) {
            'day' => $daysLogged * $rate,
            'fixed' => $rate,
            default => $totalHours * $rate
        };
    }

    private function periodsPerYear(?string $frequency): int
    {
        return match ($frequency) {
            'Monthly' => 12,
            'Semi-Monthly' => 24,
            'Weekly' => 52,
            'Bi-Weekly' => 26,
            default => 12
        };
    }

    private function calculateAnnualTax(float $taxable): float
    {
        if ($taxable <= 250000) {
            return 0;
        }
        if ($taxable <= 400000) {
            return ($taxable - 250000) * 0.15;
        }
        if ($taxable <= 800000) {
            return 22500 + ($taxable - 400000) * 0.20;
        }
        if ($taxable <= 2000000) {
            return 102500 + ($taxable - 800000) * 0.25;
        }
        if ($taxable <= 8000000) {
            return 402500 + ($taxable - 2000000) * 0.30;
        }
        return 2202500 + ($taxable - 8000000) * 0.35;
    }
}
