<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\PayrollRun;

class ChartAccountTransaction extends Model
{
    use HasFactory;

    protected $table = 'finance_chart_account_transactions';

    protected $fillable = [
        'chart_account_id',
        'payroll_run_id',
        'transaction_date',
        'description',
        'reference',
        'debit',
        'credit',
        'balance'
    ];

    protected function casts(): array
    {
        return [
            'transaction_date' => 'date',
            'debit' => 'decimal:2',
            'credit' => 'decimal:2',
            'balance' => 'decimal:2'
        ];
    }

    public function chartAccount()
    {
        return $this->belongsTo(ChartAccount::class);
    }

    public function payrollRun()
    {
        return $this->belongsTo(PayrollRun::class);
    }
}
