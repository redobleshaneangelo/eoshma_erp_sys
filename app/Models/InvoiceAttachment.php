<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InvoiceAttachment extends Model
{
    use HasFactory;

    protected $table = 'invoices_attachments';

    protected $fillable = [
        'invoice_id',
        'attachment_name',
        'attachment_size',
        'attachment_type',
        'attachment_path',
        'created_by_user_id'
    ];

    public function invoice()
    {
        return $this->belongsTo(InvoiceHeader::class, 'invoice_id');
    }
}
