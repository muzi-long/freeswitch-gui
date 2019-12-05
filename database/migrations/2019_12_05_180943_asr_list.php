<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AsrList extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('asr_list', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('pid')->nullable()->comment('asr表记录ID');
            $table->string('unique_id')->nullable()->comment('通话ID');
            $table->string('record_file')->nullable()->comment('分段录音文件');
            $table->string('text')->nullable()->comment('识别结果');
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
        Schema::dropIfExists('asr_list');
    }
}
