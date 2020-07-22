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
            $table->unsignedBigInteger('merchant_id')->default(0)->comment('商户ID');
            $table->unsignedBigInteger('parent_id')->default(0)->comment('上级部门ID，0为一级部门');
            $table->string('name')->comment('名称');
            $table->integer('sort')->default(10)->comment('排序');
            $table->softDeletes();
            $table->timestamps();
        });
        \DB::statement("ALTER TABLE `department` comment '部门'");
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
