<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class MerchantInfo extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('merchant_info', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('merchant_id')->comment('商户ID');
            $table->string('company_name')->nullable()->comment('公司名称');
            $table->timestamp('expires_at')->nullable()->comment('到期时间');
            $table->integer('sip_num')->default(0)->comment('最大分机数量');
            $table->integer('member_num')->default(0)->comment('最大员工数量');
            $table->integer('queue_num')->default(0)->comment('最大队列数量');
            $table->decimal('money',10)->default(0)->comment('帐户余额');
            $table->timestamps();
        });
        \DB::statement("ALTER TABLE `merchant_info` comment '商户信息表'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('merchant_info');
    }
}
