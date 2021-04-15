<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class MerchantLoginLog extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('merchant_login_log', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('merchant_id')->comment('登录的会员ID');
            $table->string('ip')->comment('登录IP地址');
            $table->string('platform')->nullable()->comment('系统信息');
            $table->string('browser')->nullable()->comment('浏览器信息');
            $table->timestamps();
        });
        \DB::statement("ALTER TABLE `merchant_login_log` comment '商户员工登录日志表'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('merchant_login_log');
    }
}
