<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMonitoringAyamTable extends Migration
{
    public function up()
    {
        Schema::create('monitoring_ayam', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ayam_id')->constrained('ayam', 'id_ayam')->onDelete('cascade');
            $table->foreignId('kandang_id')->constrained('kandang', 'id_kandang')->onDelete('cascade');
            $table->date('tanggal');
            $table->decimal('skat_1_bw', 8, 2);
            $table->decimal('skat_1_dg', 8, 2);
            $table->decimal('skat_2_bw', 8, 2);
            $table->decimal('skat_2_dg', 8, 2);
            $table->decimal('skat_3_bw', 8, 2);
            $table->decimal('skat_3_dg', 8, 2);
            $table->decimal('skat_4_bw', 8, 2);
            $table->decimal('skat_4_dg', 8, 2);
            $table->decimal('body_weight', 8, 2);
            $table->decimal('daily_gain', 8, 2);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('monitoring_ayam');
    }
}
