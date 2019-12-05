<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class Asr extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('asr', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('src')->nullable()->comment('主叫');
            $table->string('dst')->nullable()->comment('被叫');
            $table->string('unique_id')->nullable()->comment('通话ID');
            $table->string('record_file')->nullable()->comment('全程录音文件');
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
        Schema::dropIfExists('asr');
    }
}
