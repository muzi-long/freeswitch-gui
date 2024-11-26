<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CallcenterQueueSip extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('queue_sip', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('queue_id')->comment('队列ID');
            $table->unsignedBigInteger('sip_id')->comment('分机ID');
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
        Schema::dropIfExists('queue_sip');
    }
}
