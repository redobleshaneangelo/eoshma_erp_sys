<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InvoiceClient extends Model
{
    use HasFactory;

    protected $table = 'invoice_clients';

    protected $fillable = [
        'name',
        'email',
        'contact_person',
        'phone',
        'status'
    ];

    public function invoices()
    {
        return $this->hasMany(InvoiceHeader::class, 'invoice_client_id');
    }
}
