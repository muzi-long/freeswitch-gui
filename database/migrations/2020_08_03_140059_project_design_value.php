<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ProjectDesignValue extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('project_design_value', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('project_id')->comment('项目ID');
            $table->unsignedBigInteger('project_design_id')->comment('表单ID');
            $table->string('data')->nullable()->comment('项目对应表单字段的值');
            $table->softDeletes();
            $table->timestamps();
        });
        \DB::statement("ALTER TABLE `project_design_value` comment '项目-客户属性中间表'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('project_design_value');
    }
}
