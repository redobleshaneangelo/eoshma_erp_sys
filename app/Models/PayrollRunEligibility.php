<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PayrollRunEligibility extends Model
{
    use HasFactory;

    protected $fillable = [
        'payroll_run_id',
        'employee_id',
        'is_eligible'
    ];

    protected function casts(): array
    {
        return [
            'is_eligible' => 'boolean'
        ];
    }
}
