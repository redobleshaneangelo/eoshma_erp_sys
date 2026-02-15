<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChartAccount extends Model
{
    use HasFactory;

    protected $table = 'finance_chart_accounts';

    protected $fillable = [
        'code',
        'name',
        'type',
        'subcategory',
        'description',
        'status',
        'total_debit',
        'total_credit',
        'balance'
    ];

    public function transactions()
    {
        return $this->hasMany(ChartAccountTransaction::class)->orderByDesc('transaction_date')->orderByDesc('id');
    }
}
