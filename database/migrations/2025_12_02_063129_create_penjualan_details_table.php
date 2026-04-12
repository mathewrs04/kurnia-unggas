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
        Schema::create('penjualan_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('penjualan_id')->constrained('penjualans')->onDelete('cascade');
            $table->foreignId('produk_id')->constrained('produks')->onDelete('cascade');
            $table->foreignId('batch_id')->nullable()->constrained('batch_pembelians')->onDelete('cascade');
            $table->foreignId('timbangan_id')->nullable()->constrained('timbangans')->onDelete('cascade'); 
            $table->foreignId('metode_pembayaran_id')->nullable()->constrained('metode_pembayarans')->nullOnDelete();     
            $table->integer('jumlah_ekor');
            $table->double('jumlah_berat')->nullable();
            $table->double('harga_satuan');
            $table->double('subtotal');
            $table->string('keterangan')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('penjualan_details');
    }
};
