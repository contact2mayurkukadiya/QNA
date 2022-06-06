<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateQuestionMasterTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('question_master', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->integer('id')->autoIncrement();
            $table->integer('user_id')->nullable($value = true)->comment('Data modification by admin');
            $table->integer('round_id')->nullable($value=true);
            $table->text('question')->nullable($value = true);
            $table->text('is_question_image')->nullable($value = true);
            $table->text('answer_a')->nullable($value = true);
            $table->text('answer_b')->nullable($value = true);
            $table->text('answer_c')->nullable($value = true);
            $table->text('answer_d')->nullable($value = true);
            $table->enum('real_answer', ['answer_a', 'answer_b', 'answer_c', 'answer_d'])->nullable($value = true);
            $table->tinyInteger('is_active')->default($value = 1);
            $table->timestamp('create_time')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->timestamp('update_time')->default(DB::raw('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'));
            $table->foreign('round_id')->references('id')->on('round_master')->onDelete('cascade')->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('question_master');
    }
}
