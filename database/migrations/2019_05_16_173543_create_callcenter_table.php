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
            $table->string('display_name')->comment('显示名称');
            $table->string('name')->unique()->comment('队列标识');
            $table->string('strategy')->comment('振铃策略：
            ring-all所有振铃
            longest-idle-agent空闲时长最长振铃
            round-robin轮循振铃
            top-down顺序振铃
            agent-with-least-talk-time通话时长最小振铃
            agent-with-fewest-calls接听最少振铃
            sequentially-by-agent-order优先级振铃
            random随机振铃');
            $table->string('moh-sound')->default('$${hold_music}')->comment('客户等待音乐');
            $table->string('record-template')->default('$${recordings_dir}/${strftime(%Y)}/${strftime(%m)}/${strftime(%d)}/${strftime(%Y-%m-%d-%H-%M-%S)}.${destination_number}.${caller_id_number}.${uuid}.wav')->comment('录音地址模板');
            $table->string('time-base-score')->default('queue')->comment('优先级相关的时间积分:queue不增加积分，system：进入系统时积分。为 queue 时，数据库members.base_score值是 进入队列的时间+cc_base_score。为 system 时members.base_score值是 应答时间+cc_base_score');
            $table->integer('max-wait-time')->default(0)->comment('最大等待时间,0为不限制');
            $table->integer('max-wait-time-with-no-agent')->default(0)->comment('无空闲坐席的等待超时时间,0为不限制');
            $table->integer('max-wait-time-with-no-agent-time-reached')->default(5)->comment('如果有电话有因为（max-wait-time-with-no-agent）的原因退出队列， 队列将在延迟一定时间不允许新的电话呼入到队列');
            $table->string('tier-rules-apply')->default('false')->comment('梯队匹配 false or true');
            $table->integer('tier-rule-wait-second')->default(300)->comment('梯队的等待时间（进入下个梯队的时间）');
            $table->string('tier-rule-wait-multiply-level')->default('true')->comment('梯队等待级别 false or true');
            $table->string('tier-rule-no-agent-no-wait')->default('false')->comment('false or true,是否跳过no-agent的梯队，(no-agent就是这个梯队中不存在状态为available的成员agent    )');
            $table->integer('discard-abandoned-after')->default(60)->comment('最大丢弃时长（丢弃超过此时长，将不可以恢复）与abandoned_resume_allowed同时生效');
            $table->string('abandoned-resume-allowed')->default('false')->comment('丢弃后是否允许恢复或者重新进入队列 false or true');
            $table->timestamps();
        });

        Schema::create('agents', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name')->nullable()->comment('坐席号码');
            $table->string('system')->default('single_box')->comment('single_box');
            $table->string('uuid')->nullable()->comment('uuid');
            $table->string('type')->default('callback')->comment('callback 或者 uuid-standby');
            $table->string('contact')->nullable()->comment('呼叫字符串');
            $table->string('status')->default('Available')->comment('坐席状态Logged Out签出，Available示闲，Available (On Demand)接通电话完成后示忙，On Break休息/示忙');
            $table->string('state')->default('Waiting')->comment('坐席呼叫状态 Idle坐席空闲中，但是不会分配话务，Waiting坐席空闲中，正在等待分配话务，In a queue call正在通话');
            $table->integer('max_no_answer')->default(0)->comment('最大无应答次数,超过次数status变为On Break状态');
            $table->integer('wrap_up_time')->default(0)->comment('通话完成间隔时间,成功处理一个通话后，多久才会有电话进入的等待时长');
            $table->integer('reject_delay_time')->default(0)->comment('拒接间隔时间,来电拒接后多久才会有电话进入的等待时长');
            $table->integer('busy_delay_time')->default(0)->comment('忙重试间隔时间，来电遇忙后多久才会有电话进入的等待时长');
            $table->integer('no_answer_delay_time')->default(0)->comment('无应答重试间隔，来电无应答后多久才会有电话进入的等待时长');
            $table->integer('last_bridge_start')->default(0);
            $table->integer('last_bridge_end')->default(0);
            $table->integer('last_offered_call')->default(0);
            $table->integer('last_status_change')->default(0);
            $table->integer('no_answer_count')->default(0);
            $table->integer('calls_answered')->default(0);
            $table->integer('talk_time')->default(0);
            $table->integer('ready_time')->default(0);
            $table->integer('external_calls_count')->default(0);
            $table->timestamps();
        });

        Schema::create('tiers', function (Blueprint $table) {
            $table->string('queue')->nullable();
            $table->string('agent')->nullable();
            $table->string('state')->default('Ready')->nullable();
            $table->integer('level')->default(1);
            $table->integer('position')->default(1);
        });

        Schema::create('members', function (Blueprint $table) {
            $table->string('queue')->nullable();
            $table->string('system')->nullable();
            $table->string('uuid');
            $table->string('session_uuid');
            $table->string('cid_number')->nullable();
            $table->string('cid_name')->nullable();
            $table->integer('system_epoch');
            $table->integer('joined_epoch');
            $table->integer('rejoined_epoch');
            $table->integer('bridge_epoch');
            $table->integer('abandoned_epoch');
            $table->integer('base_score');
            $table->integer('skill_score');
            $table->string('serving_agent')->nullable();
            $table->string('serving_system')->nullable();
            $table->string('state')->nullable();
        });

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('queue');
        Schema::dropIfExists('agents');
        Schema::dropIfExists('tiers');
        Schema::dropIfExists('members');
    }
}
