<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class Freeswitch extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('freeswitch', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name')->comment('服务器名称');
            $table->string('external_ip')->comment('外网ip');
            $table->string('internal_ip')->comment('内网ip');
            $table->integer('esl_port')->default(8021)->comment('esl端口');
            $table->string('esl_password')->default('ClueCon')->comment('esl密码');
            $table->integer('internal_sip_port')->default(5060)->comment('呼出端口/注册端口，默认5060');
            $table->integer('swoole_http_port')->default(9501)->comment('生成静态配置文件的url端口');
            $table->string('fs_install_path')->default('/usr/local/freeswitch')->comment('fs安装目录');
            $table->string('fs_record_path')->default('/www/wwwroot/recordings')->comment('fs录音');
            $table->softDeletes();
            $table->timestamps();
        });
        \DB::statement("ALTER TABLE `freeswitch` comment 'fs服务器'");

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('freeswitch');
    }
}
