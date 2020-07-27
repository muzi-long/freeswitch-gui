<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class Sip extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sip', function (Blueprint $table) {
            $table->increments('id');
            $table->string('username')->unique()->comment('分机号');
            $table->string('password')->comment('分机密码');
            $table->string('context')->comment('拨号文本')->default('default');
            $table->string('effective_caller_id_name')->nullable()->comment('外显名称，针对分机与分机');
            $table->string('effective_caller_id_number')->nullable()->comment('外显号码，针对分机与分机');
            $table->string('outbound_caller_id_name')->nullable()->comment('出局名称，针对中继');
            $table->string('outbound_caller_id_number')->nullable()->comment('出局名称，针对中继');
            $table->string('state')->default('DOWN')->comment('呼叫状态,DOWN => 空闲,HANGUP => 空闲,RINGING => 响铃,RING_WAIT => 响铃,EARLY => 响铃,ACTIVE => 通话中');
            $table->tinyInteger('status')->default(0)->comment('注册状态，0未注册，1已注册');
            $table->dateTime('last_register_time')->nullable()->comment('注册时间');
            $table->dateTime('last_unregister_time')->nullable()->comment('注销时间');
            $table->unsignedBigInteger('freeswitch_id')->default(0)->comment('Freeswitch ID');
            $table->unsignedBigInteger('merchant_id')->default(0)->comment('商户ID');
            $table->unsignedBigInteger('gateway_id')->default(0)->comment('网关ID');
            $table->unsignedBigInteger('staff_id')->default(0)->comment('员工ID');
            $table->unsignedBigInteger('rate_id')->default(0)->comment('费率ID');
            $table->timestamps();
        });
        \DB::statement("ALTER TABLE `sip` comment '分机'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('sip');
    }
}
