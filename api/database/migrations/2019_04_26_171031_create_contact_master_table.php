<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateContactMasterTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('contact_master', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->integer('id')->autoIncrement();
            $table->unsignedInteger('sender_user_id')->nullable($value = true)->comment('Contact by user');
            $table->unsignedInteger('answer_user_id')->nullable($value = true)->comment('Answer by admin');
            $table->text('subject')->nullable($value = true);
            $table->text('description')->nullable($value = true);
            $table->text('answer')->nullable($value = true);
            $table->tinyInteger('is_active')->default($value = 1);
            $table->timestamp('create_time')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->timestamp('update_time')->default(DB::raw('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'));
            $table->foreign('sender_user_id')->references('id')->on('user_master')->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('answer_user_id')->references('id')->on('user_master')->onDelete('cascade')->onUpdate('cascade');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('contact_master');
    }
}
