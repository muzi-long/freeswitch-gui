<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

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
            $table->string('uuid')->comment('通话记录唯一ID');
            $table->string('aleg_uuid')->nullable()->comment('主叫ID');
            $table->string('bleg_uuid')->nullable()->comment('被叫ID');
            $table->unsignedBigInteger('department_id')->default(0)->comment('部门ID');
            $table->unsignedBigInteger('user_id')->default(0)->comment('用户ID');
            $table->unsignedBigInteger('sip_id')->default(0)->comment('分机ID');
            $table->string('department_name')->nullable()->comment('部门名称-冗余字段');
            $table->string('user_nickname')->nullable()->comment('员工名称-冗余字段');
            $table->string('caller')->comment('主叫号码');
            $table->string('callee')->comment('被叫号码');
            $table->dateTime('start_time')->nullable()->comment('主叫接听叫时间');
            $table->dateTime('answer_time')->nullable()->comment('被叫接听时间');
            $table->dateTime('end_time')->nullable()->comment('呼叫结束时间');
            $table->unsignedInteger('billsec')->default(0)->comment('呼叫时长，单位：秒');
            $table->string('record_file')->nullable()->comment('录音地址');
            $table->text('user_data')->nullable()->comment('扩展数据');
            $table->unsignedBigInteger('gateway_id')->default(0)->comment('网关ID');
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
        Schema::dropIfExists('cdr');
    }
}
