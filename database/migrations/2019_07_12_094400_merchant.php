<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

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
            $table->increments('id');
            $table->uuid('uuid');
            $table->string('username')->unique()->comment('商户帐号');
            $table->string('password')->comment('商户密码');
            $table->tinyInteger('status')->default(1)->comment('商户状态:1正常，2禁用');
            $table->string('company_name')->nullable()->comment('公司名称');
            $table->timestamp('expires_at')->nullable()->comment('到期时间');
            $table->integer('sip_num')->default(0)->comment('可添加的分机数量');
            $table->decimal('money',10)->default(0)->comment('帐户余额');
            $table->unsignedInteger('created_user_id')->default(0)->comment('创建用户ID');
            $table->timestamps();
            $table->softDeletes();
        });
        \DB::statement("ALTER TABLE `merchant` comment '商户表'");
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
