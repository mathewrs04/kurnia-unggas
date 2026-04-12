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
        Schema::create('pembelian_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pembelian_id')->constrained('pembelians')->onDelete('cascade');
            $table->foreignId('produk_id')->constrained('produks')->onDelete('cascade');
            $table->foreignId('batch_pembelian_id')->constrained('batch_pembelians')->onDelete('cascade');
            $table->foreignId('timbangan_id')->constrained('timbangans')->onDelete('cascade');
            $table->foreignId('delivery_order_id')->nullable()->constrained('delivery_orders')->onDelete('cascade');
            $table->foreignId('metode_pembayaran_id')->nullable()->constrained('metode_pembayarans')->nullOnDelete();
            $table->integer('harga_beli_per_kg')->nullable();
            $table->integer('subtotal')->nullable();
            $table->date('tanggal_bayar')->nullable();
            $table->double('susut_kg')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pembelian_details');
    }
};
