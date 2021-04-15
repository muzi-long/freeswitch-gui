<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class Digits extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('digits', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('ivr_id')->comment('IVR ID');
            $table->string('action')->comment('action应用:menu-exec-app、menu-sub、menu-top');
            $table->string('digits')->comment('用户按键数值，0-9或者正则表达式');
            $table->string('param')->nullable()->comment('应用执行参数，可为空');
            $table->timestamps();
        });
        \DB::statement("ALTER TABLE `digits` comment '按键表'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('digits');
    }
}
