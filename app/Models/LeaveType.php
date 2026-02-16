<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LeaveType extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'days_per_year',
        'is_active',
    ];

    protected $casts = [
        'days_per_year' => 'integer',
        'is_active' => 'boolean',
    ];
}