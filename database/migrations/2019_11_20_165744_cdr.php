<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class Cdr extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cdr', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('uuid')->nullable()->comment('uuid');
            $table->string('direction')->nullable()->comment('呼出/呼入');
            $table->string('src')->nullable()->comment('主叫号码');
            $table->string('dst')->nullable()->comment('主叫号码');
            $table->integer('duration')->default(0)->comment('主叫时长（秒）');
            $table->integer('billsec')->default(0)->comment('被叫时长（秒）');
            $table->dateTime('start_at')->nullable()->comment('开始时间');
            $table->dateTime('answer_at')->nullable()->comment('应答时间');
            $table->dateTime('end_at')->nullable()->comment('结束时间');
            $table->string('record_file')->nullable()->comment('通话录音');
            $table->string('user_data')->nullable()->comment('扩展数据，json格式');
            $table->string('hangup_cause')->nullable()->comment('挂机描述');
            $table->timestamps();
        });
        \DB::statement("ALTER TABLE `cdr` comment '通话记录表'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('cdr');
    }
}
