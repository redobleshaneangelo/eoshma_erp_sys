<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InvoiceLine extends Model
{
    use HasFactory;

    protected $table = 'invoices_lines';

    protected $fillable = [
        'invoice_id',
        'line_no',
        'description',
        'quantity',
        'unit',
        'unit_price',
        'line_total',
        'chart_account_id'
    ];

    protected function casts(): array
    {
        return [
            'quantity' => 'decimal:4',
            'unit_price' => 'decimal:2',
            'line_total' => 'decimal:2'
        ];
    }

    public function invoice()
    {
        return $this->belongsTo(InvoiceHeader::class, 'invoice_id');
    }

    public function chartAccount()
    {
        return $this->belongsTo(ChartAccount::class, 'chart_account_id');
    }
}
