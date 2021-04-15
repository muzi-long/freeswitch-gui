<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ProjectDesign extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('project_design', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('merchant_id')->default(0)->comment('商户ID');
            $table->string('field_label')->comment('字段名称');
            $table->string('field_key')->comment('字段标识');
            $table->string('field_type')->default('input')->comment('字段类型');
            $table->string('field_option')->nullable()->comment('字段配置项');
            $table->string('field_value')->nullable()->comment('字段默认值');
            $table->string('field_tips')->nullable()->default(null)->comment('提示信息');
            $table->tinyInteger('sort')->default(10)->comment('排序');
            $table->tinyInteger('visiable')->default(1)->comment('可见性，1显示，2隐藏。默认1');
            $table->tinyInteger('required')->default(1)->comment('是否必填，1是，2否。默认2');
            $table->softDeletes();
            $table->timestamps();
        });
        \DB::statement("ALTER TABLE `project_design` comment '项目-客户属性'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('project_design');
    }
}
