<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class Bill extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('bill', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('merchant_id')->comment('商户ID');
            $table->tinyInteger('type')->comment('流水类型：1增加，2减少');
            $table->decimal('money',10)->default(0)->comment('金额');
            $table->text('remark')->comment('备注');
            $table->unsignedInteger('created_user_id')->default(0)->comment('user用户ID');
            $table->timestamps();
        });
        \DB::statement("ALTER TABLE `bill` comment '商户帐单表'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('bill');
    }
}
