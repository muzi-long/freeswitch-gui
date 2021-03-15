<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

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
            $table->bigIncrements('id');
            $table->string('username')->unique()->comment('分机号');
            $table->string('password')->comment('分机密码');
            $table->string('state')->default('DOWN')->comment('呼叫状态,\'DOWN\' => \'空闲\',\'RINGING\' => \'响铃\',\'ACTIVE\' => \'通话中\',\'HANGUP\' => \'已挂断\',');
            $table->tinyInteger('status')->default(0)->comment('注册状态，0未注册，1已注册');
            $table->unsignedBigInteger('gateway_id')->default(0)->nullable()->comment('网关ID');
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
        Schema::dropIfExists('sip');
    }
}
