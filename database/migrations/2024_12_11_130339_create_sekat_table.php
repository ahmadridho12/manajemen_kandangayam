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
        Schema::create('sekat', function (Blueprint $table) {
            $table->id('id_sekat'); // Mengganti id menjadi sekat_id
            $table->foreignId('kandang_id')->constrained('kandang', 'id_kandang')->onDelete('cascade');
            $table->string('nama_sekat');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sekat');
    }
};
