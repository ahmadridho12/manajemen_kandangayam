<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
{
    Schema::table('kandang', function (Blueprint $table) {
        $table->unsignedTinyInteger('jumlah_sekat')->default(0)->after('nama_kandang');
    });
}

public function down()
{
    Schema::table('kandang', function (Blueprint $table) {
        $table->dropColumn('jumlah_sekat');
    });
}

};
