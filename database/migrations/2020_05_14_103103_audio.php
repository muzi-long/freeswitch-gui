<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class Audio extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('audio', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('text')->nullable()->comment('待合成文本');
            $table->string('url')->nullable()->comment('合成的语音文件的url');
            $table->string('path')->nullable()->comment('合成的语音文件的完整路径');
            $table->unsignedBigInteger('user_id')->comment('操作用户');
            $table->timestamps();
        });
        \DB::statement("ALTER TABLE `audio` comment '语音合成'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('audio');
    }
}
