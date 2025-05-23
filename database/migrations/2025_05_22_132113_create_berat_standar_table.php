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
        Schema::create('berat_standar', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('hari_ke');
            $table->decimal('bw', 8, 2); // Berat badan standar
            $table->decimal('dg', 8, 2); // Daily gain standar
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('berat_standar');
    }
};
