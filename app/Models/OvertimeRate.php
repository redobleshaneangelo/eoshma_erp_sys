<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OvertimeRate extends Model
{
    use HasFactory;

    protected $fillable = [
        'day_type',
        'label',
        'multiplier',
        'is_active'
    ];

    protected function casts(): array
    {
        return [
            'multiplier' => 'decimal:2',
            'is_active' => 'boolean'
        ];
    }
}
