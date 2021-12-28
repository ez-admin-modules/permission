<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CreateAdminerRolesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('adminer_roles', function (Blueprint $table) {
            $table->unsignedInteger('adminer_id')->index('user_id')->comment('用户ID');
            $table->unsignedInteger('role_id')->index('role_id')->comment('角色ID');
        });
        DB::statement("ALTER TABLE `adminer_roles` comment '用户角色表'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('adminer_roles');
    }
}
