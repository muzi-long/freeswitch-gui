<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateExtensionTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('extension', function (Blueprint $table) {
            $table->increments('id');
            $table->tinyInteger('sort')->default(0)->comment('序号');
            $table->string('display_name')->comment('名称');
            $table->string('name')->unique()->comment('标识符');
            $table->string('continue')->default("false")->comment('true:表示不管该extension中是否有condition匹配，都继续执行dialplan。false：表示如果该extension中有匹配的condition，那么就停止了dialplan。false是默认值');
            $table->string('context')->default('default')->comment('标识呼出还是呼入，default==呼出，public==呼入');
            $table->timestamps();
        });
        \DB::statement("ALTER TABLE `extension` comment '拨号计划'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('extension');
    }
}
