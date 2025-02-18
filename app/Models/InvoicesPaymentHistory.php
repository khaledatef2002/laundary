<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InvoicesPaymentHistory extends Model
{
    protected $guarded = [];

    public function Invoice()
    {
        return $this->belongsTo(Invoice::class);
    }
}
