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
        Schema::create('setorans', function (Blueprint $table) {
            $table->id();
            $table->string('kode_setoran')->unique();
            $table->date('tanggal_setoran');
            $table->unsignedBigInteger('nominal');
            $table->enum('status', ['menunggu_acc', 'disetujui'])->default('menunggu_acc');
            $table->text('keterangan')->nullable();
            $table->foreignId('kasir_id')->constrained('users');
            $table->foreignId('acc_by')->nullable()->constrained('users');
            $table->timestamp('acc_at')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('setorans');
    }
};
