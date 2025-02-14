<?php

namespace App\Models;

use App\Enum\DiscountType;
use Illuminate\Database\Eloquent\Model;

class InvoicesService extends Model
{
    protected $guarded = ['id'];

    public function invoice()
    {
        return $this->belongsTo(Invoice::class);
    }

    // To do
    // calc discount
    public function getDiscountAmountAttribute()
    {
        if($this->discount_type == DiscountType::FIXED->value)
        {
            return min($this->discount, $this->subtotal);
        }
        else
        {
            $discount = round(($this->discount * $this->subtotal) / 100, 2);
            return min($discount, $this->subtotal);
        }
    }
    // calc total
    public function getTotalAmountAttribute()
    {
        return $this->subtotal - $this->discount_amount;
    }
    // calc subtotal
    public function getSubtotalAttribute()
    {
        return $this->price * $this->quantity;
    }
}
