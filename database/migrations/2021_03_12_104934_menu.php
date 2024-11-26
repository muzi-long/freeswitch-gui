<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class Menu extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('menu', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name')->comment('菜单名称');
            $table->unsignedBigInteger('parent_id')->default(0)->comment('上级菜单,默认0为顶级菜单');
            $table->string('route')->nullable()->comment('路由名称，优先取链接');
            $table->string('url')->nullable()->comment('链接地址');
            $table->string('icon')->nullable()->comment('图标');
            $table->unsignedInteger('sort')->nullable()->comment('排序');
            $table->unsignedTinyInteger('type')->default(1)->comment('类型，1菜单、2按钮');
            $table->unsignedTinyInteger('permission_id')->nullable()->comment('对应权限ID');
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
        Schema::dropIfExists('menu');
    }
}
