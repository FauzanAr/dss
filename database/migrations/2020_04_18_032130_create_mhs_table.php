<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMhsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('mhs', function (Blueprint $table) {
            $table->id();
            $table->string('nama_mahasiswa', 200)->unique();
            $table->float('ipk', 4, 2);
            $table->bigInteger('tagihan_listrik');
            $table->smallInteger('prestasi');
            $table->smallInteger('bahasa_asing');
            $table->bigInteger('penghasilan_orangtua');
            $table->float('score', 3, 3);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('mhs');
    }
}
