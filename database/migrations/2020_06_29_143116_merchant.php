<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class Merchant extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('merchant', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('company_name')->nullable()->comment('公司名称');
            $table->string('contact_name')->nullable()->comment('联系人');
            $table->string('contact_phone')->nullable()->comment('联系电话');
            $table->unsignedInteger('staff_num')->default(0)->comment('员工数量');
            $table->unsignedInteger('sip_num')->default(0)->comment('分机数量');
            $table->unsignedInteger('gateway_num')->default(0)->comment('网关数量');
            $table->unsignedInteger('agent_num')->default(0)->comment('坐席数量');
            $table->unsignedInteger('queue_num')->default(0)->comment('队列数量');
            $table->unsignedInteger('task_num')->default(0)->comment('任务数量');
            $table->dateTime('expire_at')->nullable()->comment('到期时间');
            $table->unsignedBigInteger('freeswitch_id')->default(0)->comment('Freeswitch ID');
            $table->bigInteger('money')->default(0)->comment('余额,单位精确到分');
            $table->softDeletes();
            $table->timestamps();
        });
        \DB::statement("ALTER TABLE `merchant` comment '商户'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('merchant');
    }
}
