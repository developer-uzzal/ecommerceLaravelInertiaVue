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
        Schema::create('products', function (Blueprint $table) {
            $table->id();   
            $table->string('title');
            $table->string('slug')->unique();
            $table->text('short_des')->nullable();
            $table->longText('long_des')->nullable();
            $table->decimal('base_price', 10, 2);
            $table->decimal('sale_price', 10, 2)->nullable();
            $table->decimal('discount', 10, 2)->nullable();
            $table->string('image');
            $table->integer('stock');
            $table->boolean('trending')->default(false);
            $table->boolean('featured')->default(false);
            $table->boolean('best_selling')->default(false);
            $table->boolean('is_active')->default(true);
            $table->unsignedBigInteger('category_id');
            $table->unsignedBigInteger('brand_id');
            $table->foreign('category_id')->references('id')->on('categories')->cascadeOnUpdate()->restrictOnDelete();
            $table->foreign('brand_id')->references('id')->on('brands')->cascadeOnUpdate()->restrictOnDelete();
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
