<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTask extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('task', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name')->comment('任务名称');
            $table->date('date_start')->nullable()->comment('任务开始日期');
            $table->date('date_end')->nullable()->comment('任务结束日期');
            $table->time('time_start')->nullable()->comment('任务开始时间');
            $table->time('time_end')->nullable()->comment('任务结束时间');
            $table->integer('gateway_id')->comment('出局网关ID');
            $table->integer('queue_id')->nullable()->comment('转接队列ID');
            $table->integer('max_channel')->default(0)->comment('最大并发');
            $table->tinyInteger('status')->default(1)->comment('状态，1-停止，2-启动，3-完成');
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
        Schema::dropIfExists('task');
    }
}
