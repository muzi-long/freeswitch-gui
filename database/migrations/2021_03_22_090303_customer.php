<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class Customer extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('customer', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('uuid')->comment('客户编号，唯一');
            $table->string('name')->nullable()->comment('客户名称');
            $table->string('contact_name')->nullable()->comment('联系人');
            $table->string('contact_phone')->nullable()->comment('联系电话');
            $table->unsignedBigInteger('created_user_id')->default(0)->comment('创建人ID');
            $table->string('created_user_nickname')->nullable()->comment('创建人姓名');
            $table->unsignedBigInteger('node_id')->default(0)->comment('当前节点ID');
            $table->string('node_name')->nullable()->comment('当前节点名称');
            $table->timestamp('follow_time')->nullable()->comment('最近跟进时间');
            $table->unsignedBigInteger('follow_user_id')->default(0)->comment('最近跟进人ID');
            $table->string('follow_user_nickname')->nullable()->comment('最近跟进人姓名');
            $table->timestamp('next_follow_time')->nullable()->comment('下次跟进时间');
            $table->unsignedBigInteger('owner_user_id')->default(0)->comment('当前所属用户ID');
            $table->string('owner_user_nickname')->nullable()->comment('当前所属用户名称');
            $table->unsignedBigInteger('owner_department_id')->default(0)->comment('当前所属用户部门ID');
            $table->unsignedBigInteger('assignment_user_id')->default(0)->comment('分配人ID');
            $table->string('assignment_user_nickname')->nullable()->comment('分配人名称');
            $table->tinyInteger('status')->default(1)->comment('资源所在库，1待分配库（录入库），2经理库，3个人库，4部门库，5公海库');
            $table->dateTime('status_time')->nullable()->comment('进入当前库的时间');
            $table->text('remark')->nullable()->comment('客户跟进备注');
            $table->tinyInteger('is_end')->default(0)->comment('是否成单，0未成单，1成单');
            $table->timestamp('end_time')->nullable()->comment('成单时间');
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
        Schema::dropIfExists('customer');
    }
}
