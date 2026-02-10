<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Deduction extends Model
{
    use HasFactory;

    protected $fillable = [
        'payroll_run_id',
        'employee_id',
        'type',
        'amount',
        'notes'
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2'
        ];
    }

    public function employee()
    {
        return $this->belongsTo(EmployeeTest::class, 'employee_id');
    }
}
