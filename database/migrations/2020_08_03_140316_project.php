<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class Project extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('project', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('company_name')->nullable()->comment('公司名称');
            $table->string('contact_name')->nullable()->comment('联系人');
            $table->string('contact_phone')->nullable()->comment('联系电话');
            $table->unsignedBigInteger('node_id')->default(0)->comment('当前节点ID，0表示未选择节点');
            $table->timestamp('follow_at')->nullable()->comment('最近跟进时间');
            $table->unsignedBigInteger('follow_user_id')->default(0)->comment('最近跟进人ID，0表示系统跟进');
            $table->timestamp('next_follow_at')->nullable()->comment('下次跟进时间');
            $table->unsignedBigInteger('created_user_id')->default(0)->comment('创建人ID');
            $table->unsignedBigInteger('updated_user_id')->default(0)->comment('更新人ID');
            $table->unsignedBigInteger('deleted_user_id')->default(0)->comment('删除人ID');
            $table->unsignedBigInteger('owner_user_id')->default(0)->comment('拥有人ID，-1公海库，0待分配，大于0为用户ID');
            $table->unsignedBigInteger('department_id')->default(0)->comment('部门ID');
            $table->unsignedBigInteger('merchant_id')->default(0)->comment('商户ID');
            $table->softDeletes();
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
        Schema::dropIfExists('project');
    }
}
