<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateQuestionHasTag extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('question_has_tag', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('question_id');
            $table->integer('tag_id');
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
        Schema::dropIfExists('question_has_tag');
    }
}
