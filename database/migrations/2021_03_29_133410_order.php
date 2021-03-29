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
            $table->string('num')->comment('订单号');
            $table->unsignedBigInteger('customer_id')->comment('客户ID');
            $table->string('name')->nullable()->comment('客户名称');
            $table->string('contact_name')->nullable()->comment('联系人');
            $table->string('contact_phone')->nullable()->comment('联系电话');
            $table->decimal('total_money',10,2)->comment('订单总金额');
            $table->decimal('first_money',10,2)->comment('前期款');
            $table->decimal('mid_money',10,2)->comment('中期款');
            $table->decimal('last_money',10,2)->comment('尾款');
            $table->decimal('payed_money',10,2)->comment('已付金额');
            $table->unsignedBigInteger('frontend_department_id')->comment('订单成单人部门ID');
            $table->unsignedBigInteger('frontend_user_id')->comment('订单成单人ID');
            $table->string('frontend_user_nickname')->comment('订单成单人昵称');
            $table->dateTime('accept_time')->nullable()->comment('接单时间');
            $table->unsignedBigInteger('backend_department_id')->comment('订单生产人部门ID');
            $table->unsignedBigInteger('backend_user_id')->comment('订单生产人ID');
            $table->string('backend_user_nickname')->comment('订单生产人昵称');
            $table->unsignedBigInteger('created_user_id')->comment('订单创建人ID');
            $table->tinyInteger('status')->default(0)->comment('订单状态，0生产中，1已完成，2作废');

            $table->unsignedBigInteger('node_id')->default(0)->comment('当前节点ID');
            $table->string('node_name')->nullable()->comment('当前节点名称');
            $table->timestamp('follow_time')->nullable()->comment('最近跟进时间');
            $table->unsignedBigInteger('follow_user_id')->default(0)->comment('最近跟进人ID');
            $table->string('follow_user_nickname')->nullable()->comment('最近跟进人姓名');
            $table->timestamp('next_follow_time')->nullable()->comment('下次跟进时间');
            $table->text('remark')->nullable()->comment('备注');
            $table->timestamps();
        });
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
