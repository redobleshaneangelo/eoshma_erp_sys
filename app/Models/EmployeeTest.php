<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use App\Models\UsersSalaryType;

class EmployeeTest extends Model
{
    use HasFactory;

    protected $table = 'employees';

    protected $fillable = [
        'user_id',
        'payroll_group_id',
        'position',
        'rate',
        'hourly_rate'
    ];

    protected function casts(): array
    {
        return [
            'rate' => 'decimal:2',
            'hourly_rate' => 'decimal:2'
        ];
    }

    public function attendances()
    {
        return $this->hasMany(EmployeeAttendance::class, 'employee_id');
    }

    public function overtimeRequests()
    {
        return $this->hasMany(OvertimeRequest::class, 'employee_id');
    }

    public function schedules()
    {
        return $this->hasMany(EmployeeSchedule::class, 'employee_id');
    }

    public function payrollGroup()
    {
        return $this->belongsTo(PayrollGroup::class, 'payroll_group_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function salaryType()
    {
        return $this->hasOne(UsersSalaryType::class, 'user_id', 'user_id');
    }

    public function getNameAttribute(): string
    {
        if (isset($this->attributes['name'])) {
            return (string) $this->attributes['name'];
        }

        $user = $this->user;
        if (!$user) {
            return 'Unknown';
        }

        $middleInitial = $user->middle_name
            ? strtoupper(substr($user->middle_name, 0, 1)) . '.'
            : null;

        return trim(collect([
            $user->last_name . ',',
            $user->first_name,
            $middleInitial
        ])->filter()->implode(' '));
    }
}
