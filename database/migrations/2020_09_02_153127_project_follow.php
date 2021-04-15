<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ProjectFollow extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('project_follow', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('project_id')->comment('项目ID');
            $table->unsignedBigInteger('old_node_id')->comment('项目原节点ID');
            $table->unsignedBigInteger('old_node_name')->nullable()->comment('项目原节点名称');
            $table->unsignedBigInteger('new_node_id')->comment('项目新节点ID');
            $table->unsignedBigInteger('new_node_name')->nullable()->comment('项目新节点名称');
            $table->text('content')->nullable()->comment('跟进备注');
            $table->dateTime('next_follow_at')->nullable()->comment('下次跟进时间');
            $table->unsignedBigInteger('staff_id')->default(0)->comment('操作人ID');
            $table->timestamps();
        });
        \DB::statement("ALTER TABLE `project_follow` comment '项目跟进'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('project_follow');
    }
}
