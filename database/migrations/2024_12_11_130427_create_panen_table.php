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
        Schema::create('panen', function (Blueprint $table) {
            $table->id('id_panen'); // Mengganti id menjadi panen_id
            $table->foreignId('ayam_id')->constrained('ayam', 'id_ayam')->onDelete('cascade');
            $table->date('tanggal_panen');
            $table->integer('quantity');
            $table->decimal('berat_total', 8, 2);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('panen');
    }
};
