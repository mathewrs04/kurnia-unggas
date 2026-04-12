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
            $table->integer('harga_beli_per_kg')->nullable();
            $table->integer('stok_ekor');
            $table->integer('stok_ekor_minimal')->nullable();
            $table->double('stok_kg');
            $table->timestamps();
            $table->softDeletes();
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
