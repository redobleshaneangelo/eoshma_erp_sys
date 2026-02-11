<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class OvertimeRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'employee_id',
        'request_date',
        'hours',
        'day_type',
        'start_time',
        'status',
        'reason',
        'requested_by',
        'approved_by',
        'approved_at',
        'approved_multiplier',
        'approved_hours'
    ];

    protected function casts(): array
    {
        return [
            'request_date' => 'date',
            'approved_at' => 'datetime',
            'hours' => 'decimal:2',
            'approved_multiplier' => 'decimal:2',
            'approved_hours' => 'decimal:2'
        ];
    }

    public function employee()
    {
        return $this->belongsTo(EmployeeTest::class, 'employee_id');
    }

    public function requestedBy()
    {
        return $this->belongsTo(User::class, 'requested_by');
    }

    public function approvedBy()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }
}
