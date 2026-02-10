<?php

namespace App\Http\Controllers;

use App\Models\EmployeeAttendance;
use App\Models\EmployeeTest;
use App\Models\Allowance;
use App\Models\Deduction;
use App\Models\PayrollRun;
use App\Models\PayrollRunEligibility;
use App\Models\StatutoryCompliance;
use Carbon\Carbon;
use Illuminate\Http\Request;

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
            'frequency' => ['required', 'string', 'max:255'],
            'start_date' => ['required', 'date'],
            'end_date' => ['required', 'date'],
            'group' => ['required', 'string', 'max:50'],
            'status' => ['nullable', 'string', 'max:50'],
            'pay_date' => ['required', 'date'],
            'description' => ['nullable', 'string', 'max:1000']
        ]);

        if (empty($validated['status'])) {
            $validated['status'] = 'Draft';
        }

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

        $grouped = $rows->groupBy('employee_id')->map(function ($items) use ($payrollRun) {
            $employee = $items->first()->employee;
                $totalMinutes = $this->totalMinutes($items);

            return [
                'id' => $employee->id,
                'name' => $employee->name,
                'role' => $employee->position,
                'rate' => $employee->rate,
                'daysLogged' => $items->count(),
                'totalHours' => round($totalMinutes / 60, 2),
                'isEligible' => (bool) ($eligibility[$employee->id] ?? false),
                'records' => $items->map(function ($item) use ($payrollRun) {
                    return [
                        'date' => $item->attendance_date->toDateString(),
                        'timeIn' => $item->time_in,
                        'timeOut' => $item->time_out,
                        'payrollStart' => $payrollRun->start_date->toDateString(),
                        'payrollEnd' => $payrollRun->end_date->toDateString(),
                        'payrollType' => $item->payroll_type,
                        'payrollFrequency' => $item->payroll_frequency
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

        return response()->json(['data' => $rows]);
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
        $payrollRun->update(['status' => 'Pending']);

        return response()->json([
            'data' => $payrollRun
        ]);
    }

    public function approve(PayrollRun $payrollRun)
    {
        $payrollRun->update(['status' => 'Approved']);

        return response()->json([
            'data' => $payrollRun
        ]);
    }

    public function releasePayslip(PayrollRun $payrollRun)
    {
        $payrollRun->update(['status' => 'Completed']);

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

        $periodsPerYear = $this->periodsPerYear($payrollRun->frequency);
        $totalGross = 0;
        $totalTax = 0;

        $computed = $grouped->map(function ($items, $employeeId) use ($payrollRun, $allowances, $deductions, $periodsPerYear, &$totalGross, &$totalTax) {
            $employee = $items->first()->employee ?? EmployeeTest::find($employeeId);
            if (!$employee) {
                return null;
            }

            $totalMinutes = $this->totalMinutes($items);
            $totalHours = round($totalMinutes / 60, 2);
            $daysLogged = $items->count();

            $basicPay = $this->basicPay($payrollRun->group, $employee->rate, $daysLogged, $totalHours);
            $allowanceTotal = $allowances->get($employeeId, collect())->sum('amount');
            $deductionTotal = $deductions->get($employeeId, collect())->sum('amount');

            $grossPay = $basicPay + $allowanceTotal - $deductionTotal;
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

    private function shouldFilterEligible(PayrollRun $payrollRun): bool
    {
        return in_array($payrollRun->status, ['Pending', 'Approved', 'Completed'], true);
    }

    private function attendanceRows(PayrollRun $payrollRun)
    {
        return EmployeeAttendance::query()
            ->with('employee')
            ->whereBetween('attendance_date', [$payrollRun->start_date, $payrollRun->end_date])
            ->where('payroll_type', $payrollRun->group)
            ->when($payrollRun->frequency, function ($query) use ($payrollRun) {
                $query->where('payroll_frequency', $payrollRun->frequency);
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

    private function basicPay(string $group, float $rate, int $daysLogged, float $totalHours): float
    {
        return match ($group) {
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
