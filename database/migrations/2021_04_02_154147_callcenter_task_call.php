<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CallcenterTaskCall extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('task_call', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('task_id')->comment('任务ID');
            $table->string('phone')->comment('待呼叫号码');
            $table->tinyInteger('status')->default(1)->comment('1-待呼叫，2-呼叫中，3-队列等待，4-已通话');
            $table->string('uuid')->nullable()->comment('UUID');
            $table->string('aleg_uuid')->nullable()->comment('客户通话UUID');
            $table->string('bleg_uuid')->nullable()->comment('坐席通话UUID');
            $table->string('uuid')->nullable()->comment('客户通话UUID');
            $table->timestamp('datetime_originate_phone')->nullable()->comment('呼叫时间');
            $table->timestamp('datetime_entry_queue')->nullable()->comment('进入队列时间');
            $table->timestamp('datetime_sip_answer')->nullable()->comment('分机应答时间');
            $table->timestamp('datetime_end')->nullable()->comment('结束通话时间');
            $table->integer('billsec')->default(0)->comment('通话时长');
            $table->string('record_file')->nullable()->comment('录音地址');
            $table->unsignedBigInteger('sip_id')->default(0)->comment('接听分机ID');
            $table->string('sip_username')->nullable()->comment('接听分机号');
            $table->unsignedBigInteger('user_id')->default(0)->comment('接听分机的用户ID');
            $table->string('user_nickname')->nullable()->comment('接听分机的用户昵称');
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
        Schema::dropIfExists('task_call');
    }
}
