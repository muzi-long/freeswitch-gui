<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class Audio extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('audio', function (Blueprint $table) {
            $table->increments('id');
            $table->string('url')->comment('音频路径');
            $table->text('text')->comment('待合成文字');
            $table->string('auf')->comment('音频采样率：audio/L16;rate=16000 ， audio/L16;rate=8000');
            $table->string('aue')->comment('音频编码：aw（未压缩的wav格式），lame（mp3格式）');
            $table->string('voice_name')->comment('发音人：xiaoyan，aisjiuxu，aisxping，aisjinger，aisbabyxu');
            $table->string('speed')->comment('语速：50，0-100');
            $table->string('volume')->comment('音量：50，0-100');
            $table->string('pitch')->comment('音高：50，0-100');
            $table->string('engine_type')->comment('aisound（普通效果）
intp65（中文）
intp65_en（英文）
mtts（小语种，需配合小语种发音人使用）
x（优化效果）
默认为intp65');
            $table->timestamps();
        });
        \DB::statement("ALTER TABLE `audio` comment 'TTS合成表'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('audio');
    }
}
