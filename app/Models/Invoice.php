<?php

namespace App\Models;

use App\Enum\DiscountType;
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

    public function getDiscountAmountAttribute()
    {
        if($this->discount_type == DiscountType::FIXED)
        {
            return max($this->discount, $this->subtotal);
        }
        else
        {
            $discount = round(($this->discount * $this->subtotal) / 100, 2);
            return max($discount, $this->subtotal);
        }
    }

    public function getTotalAmountAttribute()
    {
        return $this->subtotal - $this->discount_amount;
    }
}
