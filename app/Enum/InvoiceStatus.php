<?php

namespace App\enum;

enum InvoiceStatus : string
{
    case DRAFT = 'draft';
    case PAID = 'paid';
    case UNPAID = 'unpaid';
    case CANCELED = 'canceled';
    case PARTIALLY_PAID = 'partially_paid';
}
