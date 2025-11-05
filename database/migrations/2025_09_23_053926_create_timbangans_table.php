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
        Schema::create('timbangans', function (Blueprint $table) {
            $table->id();
            $table->enum('jenis', ['timbangan data DO', 'timbangan data pembelian', 'timbangan data penjualan', 'timbangan stok opname']);
            $table->date('tanggal');
            $table->integer('total_jumlah_ekor');
            $table->double('total_berat');
            $table->string('nama_karyawan', 255)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('timbangans');
    }
};
