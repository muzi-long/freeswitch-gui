<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class Staff extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('staff', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('merchant_id')->default(0)->comment('商户ID');
            $table->string('username')->unique()->comment('帐号');
            $table->string('password')->comment('密码');
            $table->string('nickname',60)->nullable()->comment('姓名/昵称');
            $table->dateTime('last_login_at')->nullable()->comment('最后登录时间');
            $table->ipAddress('last_login_ip')->nullable()->comment('最后登录IP地址');
            $table->tinyInteger('is_merchant')->default(0)->comment('是否是商户');
            $table->unsignedBigInteger('department_id')->default(0)->comment('部门ID');
            $table->unsignedBigInteger('sip_id')->default(0)->comment('分机ID');
            $table->dateTime('bind_time')->nullable()->comment('分机绑定员工的时间');
            $table->softDeletes();
            $table->timestamps();
        });
        \DB::statement("ALTER TABLE `staff` comment '员工'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('staff');
    }
}
