<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCall extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('call', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('task_id')->comment('对应任务ID');
            $table->string('phone')->comment('待呼叫号码');
            $table->tinyInteger('status')->default(1)->comment('1-待呼叫，2-呼叫中，3-队列中，4-接通成功，5-接通失败');
            $table->string('uuid')->nullable()->comment('通话UUID');
            $table->timestamp('datetime_originate')->nullable()->comment('呼叫时间');
            $table->timestamp('datetime_answer')->nullable()->comment('应答时间');
            $table->timestamp('datetime_entry_queue')->nullable()->comment('转接到队列时间');
            $table->timestamp('datetime_transfer_agent')->nullable()->comment('队列转接到坐席接听时间');
            $table->string('agent')->nullable()->comment('接听坐席');
            $table->timestamp('datetime_hangup')->nullable()->comment('挂断时间');
            $table->integer('duration')->default(0)->comment('通话时长');
            $table->string('fail_cause')->nullable()->comment('失败原因');
            $table->timestamps();
            $table->foreign('task_id')->references('id')->on('task')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('call');
    }
}
