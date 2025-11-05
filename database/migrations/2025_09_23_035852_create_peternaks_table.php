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
        Schema::create('peternaks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pemasok_id')->nullable()->constrained('pemasoks')->nullOnDelete();
            $table->string('nama', 45);
            $table->string('alamat', 45);
            $table->string('no_telp', 45);
            $table->timestamps();  
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('peternaks');
    }
};
