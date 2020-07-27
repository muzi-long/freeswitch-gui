<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class Rate extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('rate', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name')->comment('费率名称');
            $table->text('description')->nullable()->comment('费率描述');
            $table->bigInteger('cost')->default(0)->comment('计费花费，单位精确到分');
            $table->unsignedInteger('time')->default(60)->comment('计费周期，默认60秒');
            $table->softDeletes();
            $table->timestamps();
        });
        \DB::statement("ALTER TABLE `rate` comment '费率'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('rate');
    }
}
