<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

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
            $table->unsignedBigInteger('parent_id')->default(0)->comment('上级部门ID，没有上级部门则默认为0');
            $table->unsignedBigInteger('merchant_id')->comment('商户ID，表示由该商户创建的部门');
            $table->timestamps();
        });
        \DB::statement("ALTER TABLE `department` comment '部门表'");
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
