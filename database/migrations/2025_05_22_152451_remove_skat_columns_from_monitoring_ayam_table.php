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
        Schema::table('monitoring_ayam', function (Blueprint $table) {
            $table->dropColumn([
                'skat_1_bw',
                'skat_1_dg',
                'skat_2_bw',
                'skat_2_dg',
                'skat_3_bw',
                'skat_3_dg',
                'skat_4_bw',
                'skat_4_dg'
            ]);
        });
    }

    public function down(): void
    {
        Schema::table('monitoring_ayam', function (Blueprint $table) {
            $table->integer('skat_1_bw')->nullable();
            $table->integer('skat_1_dg')->nullable();
            $table->integer('skat_2_bw')->nullable();
            $table->integer('skat_2_dg')->nullable();
            $table->integer('skat_3_bw')->nullable();
            $table->integer('skat_3_dg')->nullable();
            $table->integer('skat_4_bw')->nullable();
            $table->decimal('skat_4_dg', 8, 2)->nullable();
        });
    }
};
