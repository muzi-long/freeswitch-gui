<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCallcenterTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('queue', function (Blueprint $table) {
            $table->increments('id');
            $table->string('display_name')->comment('队列名称');
            $table->string('strategy')->default('top-down')->comment('振铃策略');
            $table->string('moh_sound')->default('$${hold_music}')->comment('队列语音');
            $table->string('time_base_score')->default('system');
            $table->integer('max_wait_time')->default(0)->comment('最大等待时间（超过时间未被接通将退出callcenter）0为禁用');
            $table->integer('max_wait_time_with_no_agent')->default(0)->comment('无成员（没有成员的状态是available）等待超时时间: 超出时间电话会退出callcenter 0为禁用');
            $table->integer('max_wait_time_with_no_agent_time_reached')->default(5)->comment('如果有电话有因为（max-wait-time-with-no-agent）的原因退出队列， 队列将在延迟一定时间不允许新的电话呼入到队列');
            $table->string('tier_rules_apply')->default('false')->comment('梯队匹配');
            $table->integer('tier_rule_wait_second')->default(300)->comment('梯队的等待时间（进入下个梯队的时间）');
            $table->string('tier_rule_wait_multiply_level')->default('true')->comment('梯队等待级别');
            $table->string('tier_rule_no_agent_no_wait')->default('true')->comment('是否跳过no-agent的梯队，(no-agent就是这个梯队中不存在状态为available的成员agent )');
            $table->integer('discard_abandoned_after')->default(60)->comment('最大丢弃时长（丢弃超过此时长，将不可以恢复）与abandoned_resume_allowed同时生效');
            $table->string('abandoned_resume_allowed')->default('false')->comment('丢弃后是否允许恢复或者重新进入队列');
            $table->timestamps();
        });
        \DB::statement("ALTER TABLE `queue` comment '队列表'");

        Schema::create('agent', function (Blueprint $table) {
            $table->increments('id');
            $table->string('display_name')->comment('坐席名称');
            $table->string('type')->default('callback');
            $table->string('originate_type')->default('user')->comment('呼叫类型：user-分机，group-分机组，gateway-网关');
            $table->string('originate_number')->comment('呼叫号码');
            $table->string('status')->default('Available')->comment('Logged Out签出，Available空闲，Available (On Demand)一次空闲，On Break未忙');
            $table->string('state')->nullable()->comment('坐席呼叫状态');
            $table->integer('max_no_answer')->default(3)->comment('最大无应答次数，超过次数，status变为On Break状态');
            $table->integer('wrap_up_time')->default(1)->comment('通话完成间隔时间，成功处理一个通话后，多久才会有电话进入等待时长');
            $table->integer('reject_delay_time')->default(10)->comment('挂机间隔时间，来电拒接后多久才会有电话进入的等待时长');
            $table->integer('busy_delay_time')->default(10)->comment('忙重试间隔时间，来电遇忙后多久才会有电话进入的等待时长');
            $table->integer('no_answer_delay_time')->default(10)->comment('无应答重试间隔，来电无应答后多久才会有电话进入的等待时长');
            $table->timestamps();
        });
        \DB::statement("ALTER TABLE `agent` comment '坐席表'");

        Schema::create('queue_agent', function (Blueprint $table) {
            $table->unsignedInteger('queue_id');
            $table->unsignedInteger('agent_id');

            $table->foreign('queue_id')
                ->references('id')
                ->on('queue')
                ->onDelete('cascade');
            $table->foreign('agent_id')
                ->references('id')
                ->on('agent')
                ->onDelete('cascade');
            $table->primary(['queue_id', 'agent_id']);
        });
        \DB::statement("ALTER TABLE `queue_agent` comment '队列-坐席中间表'");

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('queue_agent');
        Schema::dropIfExists('agent');
        Schema::dropIfExists('queue');
    }
}
