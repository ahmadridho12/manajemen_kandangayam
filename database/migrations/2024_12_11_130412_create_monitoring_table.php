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
        Schema::create('monitoring', function (Blueprint $table) {
            $table->id('id_monitoring'); // Mengganti id menjadi monitoring_id
            $table->foreignId('ayam_id')->constrained('ayam', 'id_ayam')->onDelete('cascade');
            $table->date('tanggal');
            $table->integer('age_days');
            $table->decimal('body_weight', 8, 2);
            $table->decimal('daily_gain', 8, 2);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('monitoring');
    }
};
