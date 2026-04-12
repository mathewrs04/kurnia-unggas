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
        Schema::create('delivery_orders', function (Blueprint $table) {
            $table->id();
            $table->string('kode_do')->unique();
            $table->foreignId('peternak_id')->nullable()->constrained('peternaks')->nullOnDelete();
            $table->integer('total_jumlah_ekor');
            $table->double('total_berat');
            $table->date('tanggal_do'); 
            $table->timestamps();
            $table->softDeletes();
        });
            
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('delivery_orders');
    }
};
