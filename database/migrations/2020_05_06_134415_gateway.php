<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class Gateway extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('gateway', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name')->comment('网关名称');
            $table->string('realm')->comment('网关IP，如果端口不是5060，默认格式为：xxx.xxx.xxx.xxx:port');
            $table->string('username')->comment('帐号');
            $table->string('password')->comment('密码');
            $table->string('prefix')->nullable()->comment('前缀');
            $table->string('outbound_caller_id')->nullable()->comment('出局号码');
            $table->tinyInteger('type')->default(1)->comment('对接方式，1=>sip,2=>ip，默认1');
            $table->timestamps();
        });
        \DB::statement("ALTER TABLE `gateway` comment '网关'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('gateway');
    }
}
