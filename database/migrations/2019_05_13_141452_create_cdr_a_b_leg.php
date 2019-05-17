<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCdrABLeg extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cdr_ab_leg', function (Blueprint $table) {
            $table->increments('id');
            $table->string('uuid')->comment('通话uuid');
            $table->string('caller_id_name')->nullable()->comment('主叫昵称');
            $table->string('caller_id_number')->nullable()->comment('主叫号码');
            $table->string('destination_number')->nullable()->comment('被叫号码');
            $table->timestamp('start_stamp')->nullable()->comment('呼叫发起时间');
            $table->timestamp('answer_stamp')->nullable()->comment('呼叫应答时间');
            $table->timestamp('end_stamp')->nullable()->comment('呼叫结束时间');
            $table->integer('duration')->default(0)->comment('总通话时长（主叫通话时长）');
            $table->integer('billsec')->default(0)->comment('接听时长（被叫通话时长）');
            $table->string('hangup_cause')->nullable()->comment('挂断原因');
            $table->string('sofia_record_file')->nullable()->comment('录音文件地址');
            $table->string('extend_content')->nullable()->comment('预留扩展字段内容');
        });
        Schema::create('cdr_a_leg', function (Blueprint $table) {
            $table->increments('id');
            $table->string('aleg_uuid')->comment('aleg_uuid');
            $table->string('bleg_uuid')->nullable()->comment('bleg_uuid');
            $table->string('caller_id_name')->nullable()->comment('主叫昵称');
            $table->string('caller_id_number')->nullable()->comment('主叫号码');
            $table->string('destination_number')->nullable()->comment('被叫号码');
            $table->timestamp('start_stamp')->nullable()->comment('呼叫发起时间');
            $table->timestamp('answer_stamp')->nullable()->comment('呼叫应答时间');
            $table->timestamp('end_stamp')->nullable()->comment('呼叫结束时间');
            $table->integer('duration')->default(0)->comment('总通话时长（主叫通话时长）');
            $table->integer('billsec')->default(0)->comment('接听时长（被叫通话时长）');
            $table->string('hangup_cause')->nullable()->comment('挂断原因');
            $table->string('sofia_record_file')->nullable()->comment('录音文件地址');
            $table->string('extend_content')->nullable()->comment('预留扩展字段内容');
        });
        Schema::create('cdr_b_leg', function (Blueprint $table) {
            $table->increments('id');
            $table->string('aleg_uuid')->comment('aleg_uuid');
            $table->string('bleg_uuid')->comment('bleg_uuid');
            $table->string('caller_id_name')->nullable()->comment('主叫昵称');
            $table->string('caller_id_number')->nullable()->comment('主叫号码');
            $table->string('destination_number')->nullable()->comment('被叫号码');
            $table->timestamp('start_stamp')->nullable()->comment('呼叫发起时间');
            $table->timestamp('answer_stamp')->nullable()->comment('呼叫应答时间');
            $table->timestamp('end_stamp')->nullable()->comment('呼叫结束时间');
            $table->integer('duration')->default(0)->comment('总通话时长（主叫通话时长）');
            $table->integer('billsec')->default(0)->comment('接听时长（被叫通话时长）');
            $table->string('hangup_cause')->nullable()->comment('挂断原因');
            $table->string('sofia_record_file')->nullable()->comment('录音文件地址');
            $table->string('extend_content')->nullable()->comment('预留扩展字段内容');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('cdr_ab_leg');
        Schema::dropIfExists('cdr_a_leg');
        Schema::dropIfExists('cdr_b_leg');
    }
}
