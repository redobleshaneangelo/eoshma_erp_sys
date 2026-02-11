<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmployeeSchedule extends Model
{
    use HasFactory;

    protected $fillable = [
        'employee_id',
        'start_time',
        'end_time'
    ];

    protected function casts(): array
    {
        return [
            'start_time' => 'string',
            'end_time' => 'string'
        ];
    }

    public function employee()
    {
        return $this->belongsTo(EmployeeTest::class, 'employee_id');
    }
}
