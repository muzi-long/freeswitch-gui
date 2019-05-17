<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateGroupSipTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('group', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name')->unique()->comment('组名标识符');
            $table->string('display_name')->comment('组名显示名称');
            $table->timestamps();
        });
        Schema::create('sip', function (Blueprint $table) {
            $table->increments('id');
            $table->string('username')->unique()->comment('分机号');
            $table->string('password')->comment('分机密码');
            $table->string('context')->comment('拨号文本')->default('default');
            $table->string('effective_caller_id_name')->nullable()->comment('外显名称，针对分机与分机');
            $table->string('effective_caller_id_number')->nullable()->comment('外显号码，针对分机与分机');
            $table->string('outbound_caller_id_name')->nullable()->comment('出局名称，针对中继');
            $table->string('outbound_caller_id_number')->nullable()->comment('出局名称，针对中继');
            $table->string('callgroup')->comment('呼叫组')->default('techsupport');
            $table->timestamps();
        });
        Schema::create('group_sip', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('group_id')->comment('组ID');
            $table->unsignedInteger('sip_id')->comment('sip用户ID');
            $table->timestamps();
            $table->foreign('group_id')->references('id')->on('group')->onDelete('cascade');
            $table->foreign('sip_id')->references('id')->on('sip')->onDelete('cascade');
        });

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('group_sip');
        Schema::dropIfExists('group');
        Schema::dropIfExists('sip');
    }
}
