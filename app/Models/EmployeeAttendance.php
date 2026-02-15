<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmployeeAttendance extends Model
{
    use HasFactory;

    protected $fillable = [
        'employee_id',
        'attendance_date',
        'time_in',
        'time_out',
        'qr_payload',
        'time_in_photo_path',
        'time_out_photo_path',
        'time_in_latitude',
        'time_in_longitude',
        'time_in_geo_accuracy',
        'time_in_location_label',
        'time_out_latitude',
        'time_out_longitude',
        'time_out_geo_accuracy',
        'time_out_location_label',
        'payroll_start',
        'payroll_end'
    ];

    protected function casts(): array
    {
        return [
            'attendance_date' => 'date',
            'payroll_start' => 'date',
            'payroll_end' => 'date',
            'time_in_latitude' => 'decimal:7',
            'time_in_longitude' => 'decimal:7',
            'time_in_geo_accuracy' => 'decimal:2',
            'time_out_latitude' => 'decimal:7',
            'time_out_longitude' => 'decimal:7',
            'time_out_geo_accuracy' => 'decimal:2'
        ];
    }

    public function employee()
    {
        return $this->belongsTo(EmployeeTest::class, 'employee_id');
    }
}
