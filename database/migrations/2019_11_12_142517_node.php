<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

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
            $table->string('name')->comment('节点名称');
            $table->tinyInteger('sort')->default(10)->comment('排序');
            $table->unsignedBigInteger('merchant_id')->comment('所属商户ID');
            $table->timestamps();
        });
        \DB::statement("ALTER TABLE `node` comment '节点表'");
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
