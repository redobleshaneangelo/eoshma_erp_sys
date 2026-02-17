<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InvoicePayment extends Model
{
    use HasFactory;

    protected $table = 'invoices_payments';

    protected $fillable = [
        'invoice_id',
        'pay_date',
        'amount',
        'reference_no',
        'attachment_name',
        'attachment_size',
        'attachment_type',
        'attachment_path',
        'posted_to_gl_at',
        'created_by_user_id'
    ];

    protected function casts(): array
    {
        return [
            'pay_date' => 'date',
            'posted_to_gl_at' => 'datetime',
            'amount' => 'decimal:2'
        ];
    }

    public function invoice()
    {
        return $this->belongsTo(InvoiceHeader::class, 'invoice_id');
    }
}
