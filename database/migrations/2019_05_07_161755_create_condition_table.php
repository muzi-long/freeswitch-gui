<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateConditionTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('condition', function (Blueprint $table) {
            $table->increments('id');
            $table->tinyInteger('sort')->default(0)->comment('序号');
            $table->string('display_name')->comment('名称');
            $table->string('field')->default('destination_number')->comment('字段,默认被叫号码');
            $table->string('expression')->nullable()->comment('正则');
            $table->string('break')->default('on-false')->comment('on-false(默认),on-true,always,never');
            $table->unsignedInteger('extension_id')->comment('所属拨号计划的ID');
            $table->timestamps();
            $table->foreign('extension_id')->references('id')->on('extension')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('condition');
    }
}
