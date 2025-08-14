<?php

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
            $table->unsignedBigInteger('customer_id');
            $table->foreign('customer_id')->references('id')->on('customer_profiles')->restrictOnDelete()->cascadeOnUpdate();
            $table->string('invoice_no');
            $table->string('name');
            $table->decimal('total', 10, 2);
            $table->decimal('vat', 10, 2)->nullable();
            $table->decimal('payable', 10, 2);
            $table->mediumText('ship_add');
            $table->unsignedBigInteger('shipping_method_id');
            $table->foreign('shipping_method_id')->references('id')->on('shipping_methods')->restrictOnDelete()->cascadeOnUpdate();
            $table->string('ship_country');
            $table->string('ship_state');
            $table->string('ship_phone');
            $table->mediumText('note')->nullable();
            $table->string('ship_mail')->nullable();
            $table->enum('delivery_status', ['pending', 'delivered', 'cancelled', 'returned'])->default('pending');
            $table->string('payment_method')->nullable();
            $table->enum('payment_status', ['paid', 'unpaid'])->default('unpaid');
            $table->enum('order_status', ['pending','processing', 'completed', 'cancelled', 'returned'])->default('pending');
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate();
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
