<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    protected $guarded = ['id', 'invoice_number'];

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function payments()
    {
        return $this->hasMany(InvoicesPaymentHistory::class);
    }

    public function service()
    {
        return $this->belongsTo(Service::class);
    }
}
