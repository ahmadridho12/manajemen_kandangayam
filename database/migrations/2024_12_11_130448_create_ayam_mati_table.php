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
        Schema::create('ayam_mati', function (Blueprint $table) {
            $table->id('id_ayam_mati'); // Mengganti id menjadi ayam_mati_id
            $table->foreignId('ayam_id')->constrained('ayam', 'id_ayam')->onDelete('cascade');
            $table->date('tanggal_mati');
            $table->string('alasan')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ayam_mati');
    }
};
