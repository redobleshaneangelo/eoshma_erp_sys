<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Client;
use App\Models\InvoiceClient;

class InvoiceHeader extends Model
{
    use HasFactory;

    protected $table = 'invoices_headers';

    protected $fillable = [
        'module',
        'invoice_no',
        'invoice_type',
        'counterparty_id',
        'invoice_client_id',
        'invoice_date',
        'due_date',
        'status',
        'total_amount',
        'net_amount',
        'paid_amount',
        'description',
        'posted_to_gl_at',
        'paid_at',
        'created_by_user_id',
        'updated_by_user_id'
    ];

    protected function casts(): array
    {
        return [
            'invoice_date' => 'date',
            'due_date' => 'date',
            'posted_to_gl_at' => 'datetime',
            'paid_at' => 'datetime',
            'total_amount' => 'decimal:2',
            'net_amount' => 'decimal:2',
            'paid_amount' => 'decimal:2'
        ];
    }

    public function lineItems()
    {
        return $this->hasMany(InvoiceLine::class, 'invoice_id')->orderBy('line_no');
    }

    public function payments()
    {
        return $this->hasMany(InvoicePayment::class, 'invoice_id')->orderBy('pay_date')->orderBy('id');
    }

    public function attachments()
    {
        return $this->hasMany(InvoiceAttachment::class, 'invoice_id')->orderBy('id');
    }

    public function counterparty()
    {
        return $this->belongsTo(Client::class, 'counterparty_id');
    }

    public function invoiceClient()
    {
        return $this->belongsTo(InvoiceClient::class, 'invoice_client_id');
    }
}
