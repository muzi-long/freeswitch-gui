<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class OrderFollow extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('order_follow', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('order_id')->comment('订单ID');
            $table->unsignedBigInteger('old_node_id')->default(0)->comment('原节点ID');
            $table->string('old_node_name')->nullable()->comment('原节点名称');
            $table->unsignedBigInteger('new_node_id')->default(0)->comment('新节点ID');
            $table->string('new_node_name')->nullable()->comment('新节点名称');
            $table->unsignedBigInteger('user_id')->default(0)->comment('跟进人ID');
            $table->string('user_name')->nullable()->comment('跟进人名称');
            $table->dateTime('next_follow_time')->nullable()->comment('下次跟进时间');
            $table->text('remark')->nullable()->comment('跟进备注');
            $table->timestamps();
        });
        \DB::statement("ALTER TABLE `order_follow` comment '订单跟进表'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('order_follow');
    }
}
