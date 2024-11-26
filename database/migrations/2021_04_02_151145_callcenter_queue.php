<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CallcenterQueue extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('queue', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name')->comment('队列名称');
            $table->string('strategy')->default('top-down')->comment('振铃策略');
            $table->integer('max_wait_time')->default(0)->comment('客户进入队列后的最大等待时间（超过时间未被接通将挂断，0为无限等待直到客户主动挂断或有坐席接听');
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
        Schema::dropIfExists('queue');
    }
}
