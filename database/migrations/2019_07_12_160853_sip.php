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
            $table->string('state')->default('DOWN')->comment('呼叫状态,\'DOWN\' => \'空闲\',\'RINGING\' => \'响铃\',\'ACTIVE\' => \'通话中\',\'HANGUP\' => \'已挂断\',');
            $table->tinyInteger('status')->default(0)->comment('注册状态，0未注册，1已注册');
            $table->unsignedBigInteger('gateway_id')->nullable()->comment('网关ID');
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
