<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('phone')->unique()->comment('电话号码，用于登录');
            $table->string('nickname')->nullable()->comment('昵称');
            $table->string('password')->comment('用户密码');
            $table->rememberToken()->comment('记住密码');
            $table->string('api_token')->nullable()->comment('token');
            $table->unsignedBigInteger('department_id')->nullable()->comment('部门ID');
            $table->dateTime('last_login_at')->nullable()->comment('最后登录时间');
            $table->ipAddress('last_login_ip')->nullable()->comment('最后登录IP地址');
            $table->timestamps();
        });
        \DB::statement("ALTER TABLE `extension` comment '用户'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users');
    }
}
