<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class Node extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('node', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('merchant_id')->default(0)->comment('商户ID');
            $table->string('name')->comment('进度节点名称');
            $table->integer('sort')->default(10)->comment('排序');
            $table->unsignedBigInteger('created_staff_id')->default(0)->comment('创建人ID，0表示后台用户创建的');
            $table->softDeletes();
            $table->timestamps();
        });
        \DB::statement("ALTER TABLE `node` comment '进度节点'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('node');
    }
}
