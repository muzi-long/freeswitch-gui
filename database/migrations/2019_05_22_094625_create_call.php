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
            $table->tinyInteger('status')->default(1)->comment('1-待呼叫，2-呼叫中，3-队列中，4-成功');
            $table->string('uuid')->nullable()->comment('通话UUID');
            $table->timestamp('datetime_originate_phone')->nullable()->comment('呼叫时间');
            $table->timestamp('datetime_entry_queue')->nullable()->comment('进入队列时间');
            $table->timestamp('datetime_agent_called')->nullable()->comment('呼叫坐席时间');
            $table->timestamp('datetime_agent_answered')->nullable()->comment('坐席应答时间');
            $table->timestamp('datetime_end')->nullable()->comment('结束通话时间');
            $table->string('agent_name')->nullable()->comment('接听坐席');
            $table->integer('billsec')->default(0)->comment('通话时长');
            $table->string('cause')->nullable()->comment('挂机原因');
            $table->timestamps();
            $table->foreign('task_id')->references('id')->on('task')->onDelete('cascade');
        });
        \DB::statement("ALTER TABLE `call` comment '呼叫任务详情表'");
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
