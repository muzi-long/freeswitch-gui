<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class Department extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('department', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name')->comment('部门名称');
            $table->tinyInteger('level')->default(1)->comment('部门级别,最高级从1开始');
            $table->unsignedBigInteger('parent_id')->default(0)->comment('上级部门ID');
            $table->unsignedBigInteger('business_user_id')->default(0)->comment('部门经理用户ID');
            $table->string('business_user_nickname')->nullable()->comment('部门经理用户昵称');
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
        Schema::dropIfExists('department');
    }
}
