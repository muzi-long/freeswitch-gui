<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class Dialplan extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('extension', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->tinyInteger('sort')->default(0)->comment('序号');
            $table->string('display_name')->comment('名称');
            $table->string('name')->unique()->comment('标识符');
            $table->string('continue')->default("false")->comment('true:表示不管该extension中是否有condition匹配，都继续执行dialplan。false：表示如果该extension中有匹配的condition，那么就停止了dialplan。false是默认值');
            $table->string('context')->default('default')->comment('标识呼出还是呼入，default==呼出，public==呼入');
            $table->timestamps();
        });
        Schema::create('condition', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->tinyInteger('sort')->default(0)->comment('序号');
            $table->string('display_name')->comment('名称');
            $table->string('field')->default('destination_number')->comment('字段,默认被叫号码');
            $table->string('expression')->nullable()->comment('正则');
            $table->string('break')->default('on-false')->comment('on-false(默认),on-true,always,never');
            $table->unsignedBigInteger('extension_id')->comment('所属拨号计划的ID');
            $table->timestamps();
            $table->foreign('extension_id')->references('id')->on('extension')->onDelete('cascade');
        });
        Schema::create('action', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->tinyInteger('sort')->default(0)->comment('序号');
            $table->string('display_name')->comment('名称');
            $table->string('application')->comment('应用');
            $table->string('data')->nullable()->comment('数据');
            $table->unsignedBigInteger('condition_id')->comment('路由规则ID');
            $table->timestamps();
            $table->foreign('condition_id')->references('id')->on('condition')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('action');
        Schema::dropIfExists('condition');
        Schema::dropIfExists('extension');
    }
}
