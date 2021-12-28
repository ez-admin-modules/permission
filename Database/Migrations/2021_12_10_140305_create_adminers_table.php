<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CreateAdminersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('adminers', function (Blueprint $table) {
            $table->id()->comment('ID');
            $table->unsignedInteger('creator_id')->default(0)->comment('创建者ID');
            $table->string('username', 20)->unique('username')->comment('管理员名');
            $table->string('nickname', 20)->comment('昵称');
            $table->string('password', 80)->comment('密码');
            $table->string('avatar')->nullable()->comment('头像');
            $table->unsignedTinyInteger('login_failure')->default(0)->comment('失败次数');
            $table->timestamp('login_at')->nullable()->comment('登录时间');
            $table->string('login_ip', 50)->nullable()->comment('登录IP');
            $table->tinyInteger('status')->default(false)->comment('状态');
            $table->timestamps();
            $table->softDeletes();
        });
        DB::statement("ALTER TABLE `adminers` comment '管理员表'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('adminers');
    }
}
