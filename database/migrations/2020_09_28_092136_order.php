<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class Order extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('order', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('company_name')->nullable()->comment('公司名称');
            $table->string('name')->nullable()->comment('姓名');
            $table->string('phone')->nullable()->comment('电话');
            $table->unsignedBigInteger('project_id')->default(0)->comment('项目ID');
            $table->unsignedBigInteger('node_id')->default(0)->comment('当前节点ID');
            $table->unsignedBigInteger('created_user_id')->default(0)->comment('成单人ID');
            $table->unsignedBigInteger('handle_user_id')->default(0)->comment('分单人ID');
            $table->dateTime('handle_time')->nullable()->comment('分单时间');
            $table->unsignedBigInteger('accept_user_id')->default(0)->comment('接单人ID');
            $table->dateTime('accept_time')->nullable()->comment('接单时间');
            $table->tinyInteger('accept_result')->default(0)->comment('是否已接单，0否，1是');
            $table->timestamp('follow_at')->nullable()->comment('最近跟进时间');
            $table->unsignedBigInteger('follow_user_id')->nullable()->comment('最近跟进人ID');
            $table->timestamp('next_follow_at')->nullable()->comment('下次跟进时间');
            $table->text('remark')->nullable()->comment('跟进备注');
            $table->softDeletes();
            $table->timestamps();
        });
        \DB::statement("ALTER TABLE `order` comment '订单表'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('order');
    }
}
