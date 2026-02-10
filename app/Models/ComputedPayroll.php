<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ComputedPayroll extends Model
{
    use HasFactory;

    protected $table = 'computed_payroll';

    protected $fillable = [
        'payroll_run_id',
        'employee_id',
        'basic_pay',
        'allowance_total',
        'deduction_total',
        'tax',
        'net_pay'
    ];

    protected function casts(): array
    {
        return [
            'basic_pay' => 'decimal:2',
            'allowance_total' => 'decimal:2',
            'deduction_total' => 'decimal:2',
            'tax' => 'decimal:2',
            'net_pay' => 'decimal:2'
        ];
    }
}
