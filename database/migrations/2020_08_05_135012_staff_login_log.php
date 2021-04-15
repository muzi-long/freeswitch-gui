<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class StaffLoginLog extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('staff_login_log', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('merchant_id')->default(0)->comment('商户ID');
            $table->string('merchant_company_name')->nullable()->comment('商户公司名称');
            $table->unsignedBigInteger('department_id')->default(0)->comment('部门ID');
            $table->string('department_name')->nullable()->comment('部门名称');
            $table->unsignedBigInteger('staff_id')->default(0)->comment('员工ID');
            $table->string('staff_nickname')->nullable()->comment('员工姓名/昵称');
            $table->string('staff_username')->nullable()->comment('员工帐号');
            $table->dateTime('time')->nullable()->comment('登录时间');
            $table->ipAddress('ip')->nullable()->comment('登录ip');
            $table->timestamps();
        });
        \DB::statement("ALTER TABLE `staff_login_log` comment '员工登录日志'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('staff_login_log');
    }
}
