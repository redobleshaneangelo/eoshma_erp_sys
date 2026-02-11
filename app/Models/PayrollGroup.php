<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PayrollGroup extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'salary_type'
    ];

    public function employees()
    {
        return $this->hasMany(EmployeeTest::class, 'payroll_group_id');
    }
}
