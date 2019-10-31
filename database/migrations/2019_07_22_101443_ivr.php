<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class Ivr extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ivr', function (Blueprint $table) {
            $table->increments('id');
            $table->string('display_name')->comment('名称');
            $table->string('name')->unique()->comment('标识');
            $table->string('greet_long')->comment('开始的欢迎音');
            $table->string('greet_short')->comment('简短提示音，用户长时间没有按键提示');
            $table->string('invalid_sound')->default('ivr/ivr-that_was_an_invalid_entry.wav')->comment('错误提示音，如果用户按错了键，则会使用该提示');
            $table->string('exit_sound')->default('voicemail/vm-goodbye.wav')->comment('菜单退出时提示音');
            $table->string('confirm_macro')->nullable()->default('')->comment('确认宏');
            $table->string('confirm_key')->nullable()->default('')->comment('确认键');
            $table->string('tts_engine')->nullable()->default('flite')->comment('语音合成引擎');
            $table->string('tts_voice')->nullable()->default('rms')->comment('语音合成声音');
            $table->string('confirm_attempts')->nullable()->default('3')->comment('确认次数');
            $table->integer('timeout')->default(10000)->comment('超时时间（毫秒），即多长时间没有收到按键就超时，播放其他提示音');
            $table->integer('inter_digit_timeout')->default(2000)->comment('两次按键的最大间隔（毫秒）');
            $table->integer('max_failures')->default(3)->comment('用户按键错误的次数');
            $table->integer('max_timeouts')->default(3)->comment('最大超时次数');
            $table->integer('digit_len')->default(4)->comment('菜单项的长度，即最大收号位数');
            $table->timestamps();
        });
        \DB::statement("ALTER TABLE `ivr` comment 'ivr语音导航'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('ivr');
    }
}
