<?php

namespace App\Models;

use App\Enum\DiscountType;
use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    protected $guarded = ['id', 'invoice_number'];

    protected $appends = ['discount_amount', 'total_amount'];

    protected static function boot()
    {
        parent::boot();

        static::creating(function($invoice){
            $invoice->invoice_number = static::generateInvoiceNumber();
        });
    }

    private static function generateInvoiceNumber()
    {
        $latestInvoice = static::latest()->first();
        $number = $latestInvoice ? ((int)substr($latestInvoice->invoice_number, -6)) + 1 : 1;
        return 'INV-' . str_pad($number, 6, '0', STR_PAD_LEFT);
    }

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
            return min($this->discount, $this->subtotal);
        }
        else
        {
            $discount = round(($this->discount * $this->subtotal) / 100, 2);
            return min($discount, $this->subtotal);
        }
    }

    public function getTotalAmountAttribute()
    {
        return $this->subtotal - $this->discount_amount;
    }
}
