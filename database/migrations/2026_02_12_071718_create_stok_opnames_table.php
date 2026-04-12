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
        Schema::create('stok_opnames', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('batch_pembelian_id')->constrained('batch_pembelians')->cascadeOnDelete();
            $table->foreignId('timbangan_id')->nullable()->constrained('timbangans')->nullOnDelete();
            $table->date('tanggal_opname');
            $table->integer('stok_ekor_sistem');
            $table->double('stok_kg_sistem');
            $table->double('berat_aktual_kg');
            $table->double('susut_kg');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stok_opnames');
    }
};
