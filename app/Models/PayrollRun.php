<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PayrollRun extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'frequency',
        'start_date',
        'end_date',
        'group',
        'payroll_group_id',
        'status',
        'pay_date',
        'description'
    ];

    protected function casts(): array
    {
        return [
            'start_date' => 'date',
            'end_date' => 'date',
            'pay_date' => 'date'
        ];
    }

    public function payrollGroup()
    {
        return $this->belongsTo(PayrollGroup::class, 'payroll_group_id');
    }
}
