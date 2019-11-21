<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ProjectRemark extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('project_remark', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('project_id')->comment('项目ID');
            $table->text('content')->nullable()->comment('备注内容');
            $table->unsignedBigInteger('merchant_id')->nullable()->comment('跟进人ID');
            $table->timestamp('next_follow_at')->nullable()->comment('下次跟进时间');
            $table->timestamps();
        });
        \DB::statement("ALTER TABLE `project_remark` comment '项目备注表'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('project_remark');
    }
}
