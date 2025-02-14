<?php

use App\Enum\DiscountType;
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
        Schema::create('invoices_services', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('invoice_id');
            $table->foreign('invoice_id')->on('invoices')->references('id')->onDelete('cascade')->onUpdate('cascade');

            $table->unsignedBigInteger('service_id');
            $table->foreign('service_id')->on('services')->references('id')->onDelete('cascade')->onUpdate('cascade');

            $table->integer('quantity');

            $table->float('price');
            
            $table->float('discount');
            $table->enum('discount_type', [DiscountType::FIXED->value, DiscountType::PERCENTAGE->value]);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invoices_services');
    }
};
