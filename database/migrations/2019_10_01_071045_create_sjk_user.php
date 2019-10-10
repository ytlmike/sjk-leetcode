<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSjkUser extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sjk_user', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name', 255)->nullable(false)->default('')->comment('用户名称');
            $table->string('leetcode_slug', 255)->nullable(false)->default('')->comment('用户的leetcode名称');
            $table->dateTime('created_at')->nullable(false)->default('0001-01-01 00:00:00')->comment('创建时间');
            $table->dateTime('updated_at')->nullable(false)->default('0001-01-01 00:00:00')->comment('创建时间');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('sjk_user');
    }
}
