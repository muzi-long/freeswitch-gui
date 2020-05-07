<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ProjectNode extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('project_node', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('project_id')->comment('项目ID');
            $table->unsignedBigInteger('old')->nullable()->comment('原节点ID');
            $table->unsignedBigInteger('new')->nullable()->comment('新节点ID');
            $table->text('content')->nullable()->comment('节点变更备注');
            $table->unsignedBigInteger('user_id')->nullable()->comment('操作人ID');
            $table->timestamps();
        });
        \DB::statement("ALTER TABLE `project_node` comment '项目节点表'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('project_node');
    }
}
