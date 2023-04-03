<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTantanganTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tantangan', function (Blueprint $table) {
            $table->id();

            $table->bigInteger('materi_id')->unsigned()->nullable();
            $table->foreign('materi_id')->references('id')->on('materi'); 

            $table->string('nama');
            
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tantangan');
    }
}
