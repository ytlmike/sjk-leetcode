<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUserSubmit extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_submit', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id')->nullable(false);
            $table->integer('question_id')->nullable(false);
            $table->dateTime('submit_at')->nullable(false)->comment('提交时间');
            $table->string('language',255)->nullable(false)->comment('使用语言');
            $table->string('result',255)->nullable(false)->comment('提交结果');
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
        Schema::dropIfExists('user_submit');
    }
}
