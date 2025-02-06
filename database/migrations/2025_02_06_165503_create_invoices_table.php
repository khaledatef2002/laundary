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

            $table->unsignedBigInteger('service_id');
            $table->foreign('service_id')->references('id')->on('services')->onDelete('cascade')->onUpdate('cascade');

            $table->integer('quantity');

            $table->float('discount');
            $table->enum('disocunt_type', [DiscountType::FIXED->value, DiscountType::PERCENTAGE->value]);

            $table->float('subtotal');

            $table->enum('status', [InvoiceStatus::CANCELED->value, InvoiceStatus::PAID->value, InvoiceStatus::UNPAID->value, InvoiceStatus::PARTIALLY_PAID->value]);
            
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
