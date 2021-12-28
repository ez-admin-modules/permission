<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CreateMenusTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('menus', function (Blueprint $table) {
            $table->id()->comment('ID');
            $table->unsignedInteger('pid')->default(0)->comment('父级ID');
            $table->unsignedInteger('creator_id')->default(0)->comment('创建者ID');
            $table->tinyInteger('type')->default(0)->comment('类型：0=目录，1=菜单，2=按钮');
            $table->string('name', 50)->comment('名称');
            $table->string('route', 200)->nullable()->comment('路由地址');
            $table->string('icon', 100)->nullable()->comment('图标');
            $table->string('component')->nullable()->comment('组件路径');
            $table->string('mark', 100)->nullable()->comment('权限标识');
            $table->tinyInteger('is_frame')->default(0)->comment('是否为外链：0=否 1=是');
            $table->tinyInteger('is_cache')->default(0)->comment('是否缓存：0=否 1=是');
            $table->tinyInteger('is_visible')->default(1)->comment('是否显示：0=否 1=是');
            $table->unsignedInteger('sort')->default(0)->comment('排序');
            $table->tinyInteger('status')->default(1)->comment('状态：0=禁用，1=启用');
            $table->timestamps();
        });
        DB::statement("ALTER TABLE `menus` comment '菜单权限表'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('menus');
    }
}
