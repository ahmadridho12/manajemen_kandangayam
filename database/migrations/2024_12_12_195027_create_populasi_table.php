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
        Schema::create('populasi', function (Blueprint $table) {
            $table->id('id_populasi');
            $table->unsignedBigInteger('populasi'); // Relasi ke tabel ayam
            $table->unsignedBigInteger('mati'); // Relasi ke tabel ayam_mati
            $table->unsignedBigInteger('panen'); // Relasi ke tabel panen
            $table->integer('total');
            $table->integer('day');
            $table->date('tanggal');
            $table->timestamps();

            // Foreign keys
            $table->foreign('populasi')->references('id_ayam')->on('ayam')->onDelete('cascade');
            $table->foreign('mati')->references('id_ayam_mati')->on('ayam_mati')->onDelete('cascade');
            $table->foreign('panen')->references('id_panen')->on('panen')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('populasi');
    }
};
