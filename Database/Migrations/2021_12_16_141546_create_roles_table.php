<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CreateRolesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('roles', function (Blueprint $table) {
            $table->id()->comment('ID');
            $table->unsignedInteger('creator_id')->default(0)->comment('创建者ID');
            $table->string('name', 20)->comment('名称');
            $table->string('mark', 20)->nullable()->comment('权限字符串');
            $table->string('description')->nullable()->comment('描述');
            $table->tinyInteger('status')->default(1)->comment('状态：0=禁用，1=启用');
            $table->timestamps();
            $table->softDeletes();
        });
        DB::statement("ALTER TABLE `roles` comment '角色表'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('roles');
    }
}
