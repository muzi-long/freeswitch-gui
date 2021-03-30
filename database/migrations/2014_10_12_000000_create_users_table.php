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
        Schema::create('user', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name')->nullable()->comment('帐号');
            $table->string('password')->nullable()->comment('密码');
            $table->string('nickname')->nullable()->comment('昵称');
            $table->string('phone')->nullable()->comment('手机号');
            $table->tinyInteger('status')->default(1)->comment('状态：1正常，2禁用，默认1');
            $table->string('last_login_ip')->nullable()->comment('最后登录ip');
            $table->string('last_login_time')->nullable()->comment('最后登录时间');
            $table->string('email')->nullable();
            $table->timestamp('email_verified_at')->nullable();
            $table->unsignedBigInteger('sip_id')->default(0)->nullable()->comment('分机ID');
            $table->unsignedBigInteger('department_id')->default(0)->nullable()->comment('部门ID');
            $table->rememberToken();
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
        Schema::dropIfExists('user');
    }
}
