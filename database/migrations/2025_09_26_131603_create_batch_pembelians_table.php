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
        Schema::create('batch_pembelians', function (Blueprint $table) {
            $table->id();
            $table->string('kode_batch');
            $table->integer('harga_beli_per_kg');
            $table->integer('stok_ekor');
            $table->integer('stok_ekor_minimal');
            $table->double('stok_kg');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     * 
     */
    public function down(): void
    {
        Schema::dropIfExists('batch_pembelians');
    }
};
