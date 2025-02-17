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
        Schema::create('ayam', function (Blueprint $table) {
            $table->id('id_ayam'); // Mengganti id menjadi ayam_id
            $table->foreignId('sekat_id')->constrained('sekat', 'id_sekat')->onDelete('cascade');
            $table->date('tanggal_masuk');
            $table->decimal('berat_awal', 8, 2);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ayam');
    }
};
