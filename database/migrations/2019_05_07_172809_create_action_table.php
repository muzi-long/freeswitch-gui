<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateActionTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('action', function (Blueprint $table) {
            $table->increments('id');
            $table->tinyInteger('sort')->default(0)->comment('序号');
            $table->string('display_name')->comment('名称');
            $table->string('application')->comment('应用');
            $table->string('data')->nullable()->comment('数据');
            $table->unsignedInteger('condition_id')->comment('路由规则ID');
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
    }
}
