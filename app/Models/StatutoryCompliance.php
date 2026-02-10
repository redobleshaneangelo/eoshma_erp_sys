<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StatutoryCompliance extends Model
{
    use HasFactory;

    protected $table = 'statutory_compliance';

    protected $fillable = [
        'payroll_run_id',
        'name',
        'coverage_start',
        'coverage_end',
        'employee_share',
        'employer_share',
        'total',
        'due_date'
    ];

    protected function casts(): array
    {
        return [
            'coverage_start' => 'date',
            'coverage_end' => 'date',
            'due_date' => 'date',
            'employee_share' => 'decimal:2',
            'employer_share' => 'decimal:2',
            'total' => 'decimal:2'
        ];
    }
}
