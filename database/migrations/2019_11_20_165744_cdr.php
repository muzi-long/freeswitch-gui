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
            $table->unsignedBigInteger('user_id')->nullable()->comment('用户ID');
            $table->string('uuid')->nullable()->comment('通话唯一标识');
            $table->string('aleg_uuid')->nullable()->comment('aleg唯一标识');
            $table->string('bleg_uuid')->nullable()->comment('bleg唯一标识');
            $table->tinyInteger('direction')->default(1)->comment('1呼出,2呼入');
            $table->string('src')->nullable()->comment('主叫号码');
            $table->string('dst')->nullable()->comment('主叫号码');
            $table->integer('duration')->default(0)->comment('A通话时长（秒）');
            $table->integer('billsec')->default(0)->comment('B通话时长（秒）');
            $table->dateTime('aleg_start_at')->nullable()->comment('主叫开始时间');
            $table->dateTime('aleg_answer_at')->nullable()->comment('主叫应答时间');
            $table->dateTime('aleg_end_at')->nullable()->comment('主叫结束时间');
            $table->dateTime('bleg_start_at')->nullable()->comment('被叫开始时间');
            $table->dateTime('bleg_answer_at')->nullable()->comment('被叫应答时间');
            $table->dateTime('bleg_end_at')->nullable()->comment('被叫结束时间');
            $table->string('record_file')->nullable()->comment('通话录音地址');
            $table->string('user_data')->nullable()->comment('扩展数据，json格式');
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
