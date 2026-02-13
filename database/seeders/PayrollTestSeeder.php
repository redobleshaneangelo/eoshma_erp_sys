<?php

namespace Database\Seeders;

use App\Models\Allowance;
use App\Models\ComputedPayroll;
use App\Models\Deduction;
use App\Models\EmployeeAttendance;
use App\Models\EmployeeTest;
use App\Models\PayrollRun;
use App\Models\StatutoryCompliance;
use App\Models\Department;
use App\Models\UsersHasBranch;
use App\Models\PayrollGroup;
use App\Models\UsersSalaryType;
use App\Models\OvertimeRequest;
use App\Models\OvertimeRate;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class PayrollTestSeeder extends Seeder
{
    public function run(): void
    {
        ComputedPayroll::query()->delete();
        StatutoryCompliance::query()->delete();
        Allowance::query()->delete();
        Deduction::query()->delete();
        EmployeeAttendance::query()->delete();

        $payrollTypes = ['hour', 'day', 'fixed'];
        $frequencies = ['Monthly', 'Semi-Monthly', 'Weekly', 'Bi-Weekly'];

        $names = [
            'David Cruz',
            'Mia Santos',
            'Leo Tan',
            'Karen Lim',
            'Ralph Yao',
            'Ana Reyes',
            'Noah Lim',
            'Jenna Cruz',
            'Paolo Reyes',
            'Kim Lee',
            'Eric Tan',
            'Lara Santos',
            'Mark Rivera',
            'Chloe Bautista',
            'Ivan Garcia',
            'Sofia dela Cruz',
            'Nico Alvarez',
            'Grace Torres',
            'Peter Goh',
            'Ava Santiago',
            'Oscar Lim',
            'Jade Villanueva',
            'Ken Morales',
            'Bea Ramos'
        ];

        $positions = [
            'Field Engineer',
            'Site Supervisor',
            'Surveyor',
            'Safety Officer',
            'Civil Foreman',
            'Project Coordinator',
            'Equipment Operator',
            'Cost Analyst',
            'Drafting Specialist',
            'Quality Inspector',
            'Warehouse Lead',
            'Admin Assistant',
            'Procurement Analyst',
            'Project Scheduler',
            'QA Specialist',
            'Logistics Coordinator',
            'Site Inspector',
            'Maintenance Lead',
            'HR Assistant',
            'Payroll Clerk',
            'Safety Engineer',
            'Planning Officer',
            'Inventory Staff',
            'Executive Assistant'
        ];

        $employees = collect();
        $index = 0;
        $employeesPerCombo = 2;

        $payrollGroups = PayrollGroup::query()->get()->keyBy('payroll_frequency');
        foreach ($frequencies as $frequency) {
            if (!$payrollGroups->has($frequency)) {
                $payrollGroups[$frequency] = PayrollGroup::create([
                    'name' => $frequency . ' Group',
                    'payroll_frequency' => $frequency
                ]);
            }
        }

        foreach ($payrollTypes as $type) {
            foreach ($frequencies as $frequency) {
                for ($i = 0; $i < $employeesPerCombo; $i++) {
                    $rate = match ($type) {
                        'day' => 800,
                        'fixed' => 28800,
                        default => 80
                    };
                    $fullName = $names[$index] ?? "Employee {$index}";
                    $nameParts = preg_split('/\s+/', trim($fullName));
                    $lastName = array_pop($nameParts) ?? 'Employee';
                    $firstName = $nameParts[0] ?? 'User';
                    $middleName = count($nameParts) > 1
                        ? implode(' ', array_slice($nameParts, 1))
                        : null;
                    $emailIndex = $index + 1;
                    $user = User::updateOrCreate(
                        ['email' => 'hrr' . $emailIndex . '@gmail.com'],
                        [
                            'account_id' => 'ACC' . str_pad((string) $emailIndex, 4, '0', STR_PAD_LEFT),
                            'last_name' => $lastName,
                            'first_name' => $firstName,
                            'middle_name' => $middleName,
                            'phone' => '09' . str_pad((string) ($emailIndex + 100000000), 9, '0', STR_PAD_LEFT),
                            'password' => Hash::make('eoshma123'),
                            'role' => 'hr_manager',
                            'status' => 1
                        ]
                    );

                    Department::updateOrCreate(
                        ['users_id' => $user->id],
                        ['name' => 'HR']
                    );

                    UsersHasBranch::updateOrCreate(
                        ['user_id' => $user->id],
                        ['branch_id' => 2]
                    );

                    $employees->push(EmployeeTest::updateOrCreate(
                        ['user_id' => $user->id],
                        [
                            'payroll_group_id' => $payrollGroups[$frequency]->id,
                            'payroll_frequency' => $frequency,
                            'position' => $positions[$index] ?? 'Staff',
                            'rate' => $rate
                        ]
                    ));

                    UsersSalaryType::updateOrCreate(
                        ['user_id' => $user->id],
                        ['salary_type' => $type]
                    );
                    $index++;
                }
            }
        }


        $payrollStart = Carbon::parse('2026-02-01');
        $payrollEnd = $payrollStart->copy()->addDays(4);

        $approverId = User::query()->value('id');
        $regularRate = OvertimeRate::query()->where('day_type', 'regular_day')->first();

        $otSeeds = [
            ['offset' => 1, 'hours' => 2.0],
            ['offset' => 3, 'hours' => 1.5],
            ['offset' => 5, 'hours' => 3.0]
        ];

        $employees->take(3)->each(function ($employee, $index) use ($payrollStart, $approverId, $regularRate, $otSeeds) {
            $seed = $otSeeds[$index] ?? ['offset' => 1, 'hours' => 2.0];
            $requestDate = $payrollStart->copy()->addDays($seed['offset'])->toDateString();

            OvertimeRequest::create([
                'employee_id' => $employee->id,
                'request_date' => $requestDate,
                'hours' => $seed['hours'],
                'day_type' => 'regular_day',
                'start_time' => '17:00:00',
                'status' => 'Approved',
                'reason' => 'Seeded overtime request',
                'requested_by' => $employee->user_id,
                'approved_by' => $approverId,
                'approved_at' => now(),
                'approved_multiplier' => $regularRate?->multiplier,
                'approved_hours' => $seed['hours']
            ]);
        });

        $dateRange = collect();
        for ($i = 0; $i <= $payrollStart->diffInDays($payrollEnd); $i++) {
            $dateRange->push($payrollStart->copy()->addDays($i));
        }

        $employees->each(function ($employee, $index) use ($payrollTypes, $frequencies, $dateRange, $payrollStart, $payrollEnd) {
            $type = $payrollTypes[intdiv($index, count($frequencies))] ?? 'hour';
            $frequency = $frequencies[$index % count($frequencies)] ?? 'Weekly';
            foreach ($dateRange as $dayIndex => $date) {
                $pattern = ($index + $dayIndex) % 5;
                $timeIn = '08:00:00';
                $timeOut = '17:00:00';

                if ($pattern === 1) {
                    $timeIn = '08:20:00';
                } elseif ($pattern === 2) {
                    $timeIn = '07:55:00';
                } elseif ($pattern === 3) {
                    $timeIn = null;
                    $timeOut = null;
                }

                EmployeeAttendance::create([
                    'employee_id' => $employee->id,
                    'attendance_date' => $date->toDateString(),
                    'time_in' => $timeIn,
                    'time_out' => $timeOut,
                    'payroll_start' => $payrollStart->toDateString(),
                    'payroll_end' => $payrollEnd->toDateString()
                ]);
            }
        });

        $runs = PayrollRun::query()->get();
        if ($runs->isEmpty()) {
            $runs = collect([
                PayrollRun::create([
                    'name' => 'Seeded Payroll Run',
                    'frequency' => 'Bi-Weekly',
                    'start_date' => '2026-02-01',
                    'end_date' => '2026-02-15',
                    'group' => 'Bi-Weekly Group',
                    'payroll_group_id' => $payrollGroups['Bi-Weekly']->id,
                    'status' => 'Draft',
                    'pay_date' => '2026-02-20',
                    'description' => 'Seeded payroll run for testing.'
                ])
            ]);
        }

        foreach ($runs as $run) {
            $attendanceRows = EmployeeAttendance::query()
                ->whereBetween('attendance_date', [$run->start_date, $run->end_date])
                ->when($run->payroll_group_id, function ($query) use ($run) {
                    $query->whereHas('employee', function ($employeeQuery) use ($run) {
                        $employeeQuery->where('payroll_group_id', $run->payroll_group_id);
                    });
                })
                ->when($run->frequency, function ($query) use ($run) {
                    $query->whereHas('employee', function ($employeeQuery) use ($run) {
                        $employeeQuery->where('payroll_frequency', $run->frequency);
                    });
                })
                ->get()
                ->groupBy('employee_id');

            $periodsPerYear = match ($run->frequency) {
                'Monthly' => 12,
                'Semi-Monthly' => 24,
                'Weekly' => 52,
                'Bi-Weekly' => 26,
                default => 12
            };

            $totalGross = 0;
            $totalTax = 0;

            foreach ($attendanceRows as $employeeId => $items) {
                $employee = EmployeeTest::find($employeeId);
                if (!$employee) {
                    continue;
                }

                $totalMinutes = $items->sum(function ($item) {
                    if (!$item->time_in || !$item->time_out) {
                        return 0;
                    }
                    $start = Carbon::createFromFormat('H:i:s', $item->time_in);
                    $end = Carbon::createFromFormat('H:i:s', $item->time_out);
                    return $end->diffInMinutes($start, true);
                });

                $totalHours = round($totalMinutes / 60, 2);
                $daysLogged = $items->count();

                $salaryType = UsersSalaryType::query()
                    ->where('user_id', $employee->user_id)
                    ->value('salary_type') ?? 'hour';

                $basicPay = match ($salaryType) {
                    'day' => $daysLogged * $employee->rate,
                    'fixed' => $employee->rate,
                    default => $totalHours * $employee->rate
                };

                $allowance = Allowance::create([
                    'payroll_run_id' => $run->id,
                    'employee_id' => $employee->id,
                    'type' => 'Meal Allowance',
                    'amount' => 500,
                    'notes' => 'Seeded allowance'
                ]);

                $deduction = Deduction::create([
                    'payroll_run_id' => $run->id,
                    'employee_id' => $employee->id,
                    'type' => 'Loan',
                    'amount' => 300,
                    'notes' => 'Seeded deduction'
                ]);

                $allowanceTotal = $allowance->amount;
                $deductionTotal = $deduction->amount;
                $grossPay = $basicPay + $allowanceTotal - $deductionTotal;
                $taxableAnnual = $grossPay * $periodsPerYear;
                $annualTax = $this->calculateAnnualTax($taxableAnnual);
                $tax = round($annualTax / $periodsPerYear, 2);
                $netPay = $grossPay - $tax;

                ComputedPayroll::create([
                    'payroll_run_id' => $run->id,
                    'employee_id' => $employee->id,
                    'basic_pay' => $basicPay,
                    'allowance_total' => $allowanceTotal,
                    'deduction_total' => $deductionTotal,
                    'tax' => $tax,
                    'net_pay' => $netPay
                ]);

                $totalGross += $grossPay;
                $totalTax += $tax;
            }

            $dueDate = Carbon::parse($run->end_date)->addMonth();
            $monthlyBase = $periodsPerYear > 0 ? ($totalGross * ($periodsPerYear / 12)) : 0;
            $philHealthBase = min($monthlyBase, 100000);

            $sssEmployee = round($monthlyBase * 0.045, 2);
            $sssEmployer = round($monthlyBase * 0.095, 2);
            $philHealthEmployee = round($philHealthBase * 0.025, 2);
            $philHealthEmployer = round($philHealthBase * 0.025, 2);
            $pagibigEmployee = round(min($monthlyBase * 0.02, 100), 2);
            $pagibigEmployer = round(min($monthlyBase * 0.02, 100), 2);

            $statutory = [
                [
                    'name' => 'SSS Contribution',
                    'employee_share' => $sssEmployee,
                    'employer_share' => $sssEmployer
                ],
                [
                    'name' => 'PhilHealth Premium',
                    'employee_share' => $philHealthEmployee,
                    'employer_share' => $philHealthEmployer
                ],
                [
                    'name' => 'Pag-IBIG Fund',
                    'employee_share' => $pagibigEmployee,
                    'employer_share' => $pagibigEmployer
                ],
                [
                    'name' => 'Withholding Tax',
                    'employee_share' => round($totalTax, 2),
                    'employer_share' => 0
                ]
            ];

            foreach ($statutory as $item) {
                StatutoryCompliance::create([
                    'payroll_run_id' => $run->id,
                    'name' => $item['name'],
                    'coverage_start' => $run->start_date,
                    'coverage_end' => $run->end_date,
                    'employee_share' => $item['employee_share'],
                    'employer_share' => $item['employer_share'],
                    'total' => $item['employee_share'] + $item['employer_share'],
                    'due_date' => $dueDate->toDateString()
                ]);
            }
        }
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
