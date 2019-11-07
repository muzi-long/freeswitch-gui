<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class Member extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('member', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('phone')->comment('手机号码');
            $table->string('password')->comment('密码');
            $table->string('nickname')->nullable()->comment('昵称');
            $table->unsignedBigInteger('sip_id')->nullable()->comment('分机ID');
            $table->unsignedBigInteger('merchant_id')->nullable()->comment('商户ID');
            $table->rememberToken();
            $table->string('api_token', 80)->unique()->nullable()->default(null);
            $table->timestamps();
        });
        \DB::statement("ALTER TABLE `member` comment '会员表'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('member');
    }
}
