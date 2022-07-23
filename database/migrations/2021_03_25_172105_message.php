<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class Message extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('message', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('send_user_id')->default(0)->comment('发送消息用户ID');
            $table->string('send_user_nickname')->nullable()->comment('发送消息用户昵称');
            $table->unsignedBigInteger('accept_user_id')->default(0)->comment('接收消息用户ID');
            $table->string('accept_user_nickname')->nullable()->comment('接收消息用户昵称');
            $table->string('title')->nullable()->comment('消息标题');
            $table->text('content')->nullable()->comment('消息内容');
            $table->tinyInteger('read')->default(0)->comment('消息状态：0未读 1已读，默认0');
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
        Schema::dropIfExists('message');
    }
}
