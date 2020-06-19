<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class Admin extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('admin', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('nickname',60)->nullable()->comment('姓名/昵称');
            $table->string('username',100)->unique()->comment('帐号');
            $table->string('password')->comment('密码');
            $table->string('phone')->unique()->comment('电话号码');
            $table->rememberToken()->comment('记住密码');
            $table->string('api_token')->nullable()->comment('token');
            $table->dateTime('last_login_at')->nullable()->comment('最后登录时间');
            $table->ipAddress('last_login_ip')->nullable()->comment('最后登录IP地址');
            $table->timestamps();
        });
        \DB::statement("ALTER TABLE `admin` comment '后台用户'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('admin');
    }
}
