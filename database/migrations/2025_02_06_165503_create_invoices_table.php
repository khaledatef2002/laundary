<?php

use App\Enum\DiscountType;
use App\enum\InvoiceStatus;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('invoices', function (Blueprint $table) {
            $table->id();

            $table->string('invoice_number')->unique();

            $table->unsignedBigInteger('client_id');
            $table->foreign('client_id')->references('id')->on('clients')->onDelete('cascade')->onUpdate('cascade');

            $table->float('discount');
            $table->enum('discount_type', [DiscountType::FIXED->value, DiscountType::PERCENTAGE->value]);

            $table->enum('status', 
                [
                InvoiceStatus::DRAFT->value, 
                InvoiceStatus::CANCELED->value, 
                InvoiceStatus::PAID->value, 
                InvoiceStatus::UNPAID->value, 
                InvoiceStatus::PARTIALLY_PAID->value])
                ->default(InvoiceStatus::DRAFT->value);
            
            $table->date('due_date');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invoices');
    }
};
