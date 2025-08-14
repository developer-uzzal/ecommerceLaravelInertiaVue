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
        Schema::create('header_footers', function (Blueprint $table) {
            $table->id();
            $table->string('logo');
            $table->string('favicon')->nullable();
            $table->string('phone');
            $table->mediumText('messenger')->nullable();
            $table->mediumText('whatsapp')->nullable();
            $table->string('mail');
            $table->mediumText('address');
            $table->mediumText('short_desc');
            $table->mediumText('copy_right');
            $table->string('social1')->nullable();
            $table->string('social2')->nullable();
            $table->string('social3')->nullable();
            $table->string('social4')->nullable();
            $table->string('social5')->nullable();
            $table->string('social6')->nullable();
            $table->string('payment1')->nullable();
            $table->string('payment2')->nullable();
            $table->string('payment3')->nullable();
            $table->string('payment4')->nullable();
            $table->string('payment5')->nullable();
            $table->string('payment6')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('header_footers');
    }
};
