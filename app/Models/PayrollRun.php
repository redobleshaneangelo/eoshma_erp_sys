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
}
