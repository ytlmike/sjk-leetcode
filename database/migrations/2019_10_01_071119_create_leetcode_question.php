<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLeetcodeQuestion extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('leetcode_question', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('leetcode_id')->nullable(false)->comment('leetcdoe题目ID');
            $table->integer('difficulty_level')->nullable(false)->comment('难度等级');
            $table->string('question_name')->nullable(false)->default('')->comment('题目名称');
            $table->string('question_slug')->nullable(false)->default('')->comment('题目slug');
            $table->string('translated_name')->nullable(false)->default('')->comment('中文名称');
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
        Schema::dropIfExists('leetcode_question');
    }
}
