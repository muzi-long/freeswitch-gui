<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class MerchantGateway extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('merchant_gateway', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('merchant_id')->comment('商户ID');
            $table->unsignedInteger('gateway_id')->comment('网关ID');
            $table->decimal('rate',10,2)->comment('每分钟多少钱，单位：元。');
        });
        \DB::statement("ALTER TABLE `merchant_gateway` comment '商户-网关多对多表'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('merchant_gateway');
    }
}
