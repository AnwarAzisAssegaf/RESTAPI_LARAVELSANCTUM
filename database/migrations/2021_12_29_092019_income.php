<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class Income extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('income', function (Blueprint $table) {
            $table->increments('id');
            $table->string('transaksi');
            $table->double('jumlah');
            $table->longText('file_bukti');
            $table->date('tanggal');
            $table->string('bulanx');
            $table->string('tahunx');
            $table->date('tanggal_buat');
            $table->date('user_id');
            $table->string('stts_lock');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('income');
    }
}
