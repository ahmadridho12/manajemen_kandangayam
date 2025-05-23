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
        Schema::create('monitoring_sekat', function (Blueprint $table) {
    $table->id('id_monitoring_sekat');
    $table->unsignedBigInteger('monitoring_id');
    $table->unsignedBigInteger('sekat_id');
    
    $table->decimal('body_weight', 8, 2)->nullable();
    $table->decimal('daily_gain', 8, 2)->nullable();

    $table->timestamps();

    // Foreign key ke monitoring_ayam
    $table->foreign('monitoring_id')->references('id')->on('monitoring_ayam')->onDelete('cascade');

    // Foreign key ke sekat dengan id_sekat
    $table->foreign('sekat_id')->references('id_sekat')->on('sekat')->onDelete('cascade');
});

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('monitoring_sekat');
    }
};
