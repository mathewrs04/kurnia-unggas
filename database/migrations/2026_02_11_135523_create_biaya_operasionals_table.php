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
        Schema::create('biaya_operasionals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('no_nota');
            $table->foreignId('produk_id')->nullable()->constrained('produks')->nullOnDelete();
            $table->foreignId('metode_pembayaran_id')->nullable()->constrained('metode_pembayarans')->nullOnDelete();
            $table->date('tanggal_biaya');
            $table->string('foto_nota');
            $table->integer('harga_satuan');
            $table->integer('jumlah');
            $table->integer('subtotal');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('biaya_operasionals');
    }
};
