<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCdrAsrTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cdr_asr', function (Blueprint $table) {
            $table->increments('id');
            $table->string('uuid')->comment('当前识别通道uuid');
            $table->string('text')->comment('识别结果');
            $table->timestamps();
        });
        \DB::statement("ALTER TABLE `cdr_asr` comment 'asr语音识别表'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('cdr_asr');
    }
}
