<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateQuestionTag extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('question_tag', function (Blueprint $table) {
            $table->increments('id');
            $table->string('tag_name',255)->default('')->comment('标签名称');
            $table->string('translated_name', 255)->default('')->comment('中文名');
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
        Schema::dropIfExists('question_tag');
    }
}
