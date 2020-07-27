<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

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
            $table->bigIncrements('id');
            $table->unsignedBigInteger('merchant_id')->default(0)->comment('商户ID');
            $table->string('merchant_name')->nullable()->comment('商户名称');
            $table->smallInteger('type')->comment('流水类型：1增加，2减少');
            $table->bigInteger('money')->default(0)->comment('增加或减少的金额，单位精确到分');
            $table->bigInteger('total')->comment('增加或减少后剩余的总金额，与merchant表里的mongy值保持一致');
            $table->string('remark')->nullable()->comment('备注');
            $table->unsignedBigInteger('admin_id')->default(0)->comment('操作人ID（后台用户）,0为系统操作');
            $table->string('admin_name')->nullable()->comment('操作人名称');
            $table->timestamps();
        });
        \DB::statement("ALTER TABLE `bill` comment '商户帐单流水'");
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
