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
            $table->string('username')->unique()->comment('帐号');
            $table->string('password')->comment('密码');
            $table->string('contact_name')->nullable()->comment('联系人');
            $table->string('contact_phone')->nullable()->comment('联系电话');
            $table->tinyInteger('status')->default(1)->comment('状态:1正常，2禁用');
            $table->integer('merchant_id')->default(0)->comment('0为商户，非0为员工时值为员工的商户ID');
            $table->unsignedBigInteger('sip_id')->default(0)->comment('分机号ID');
            $table->rememberToken();
            $table->string('api_token', 80)->unique()->nullable()->default(null);
            $table->timestamps();
            $table->softDeletes();
        });
        \DB::statement("ALTER TABLE `merchant` comment '商户员工表'");
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
