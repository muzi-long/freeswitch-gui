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
            $table->string('company_name')->nullable()->comment('公司名称');
            $table->string('contact_name')->nullable()->comment('联系人');
            $table->string('contact_phone')->nullable()->comment('联系电话');
            $table->tinyInteger('status')->default(1)->comment('状态:1正常，2禁用');
            $table->timestamp('expires_at')->nullable()->comment('到期时间');
            $table->integer('sip_num')->default(0)->comment('最大分机数量');
            $table->integer('member_num')->default(0)->comment('最大子帐号数量');
            $table->integer('queue_num')->default(0)->comment('最大队列数量');
            $table->decimal('money',10)->default(0)->comment('帐户余额');
            $table->rememberToken();
            $table->string('api_token', 80)->unique()->nullable()->default(null);
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
