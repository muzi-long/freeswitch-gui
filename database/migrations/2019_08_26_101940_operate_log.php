<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class OperateLog extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('operate_log', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('user_id')->comment('操作用户ID');
            $table->string('uri')->comment('操作地址');
            $table->string('parameter')->nullable()->comment('参数');
            $table->string('method')->comment('请求方式：GET、POST、PUT、DELETE、HEAD');
            $table->timestamps();
        });
        \DB::statement("ALTER TABLE `operate_log` comment '操作日志'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('operate_log');
    }
}
