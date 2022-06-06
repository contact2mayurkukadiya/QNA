<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRoundMasterTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('round_master', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->integer('id')->autoIncrement();
            $table->integer('user_id')->nullable($value = true)->comment('Data modification by admin');
            $table->string('round_name')->nullable($value = true);
            $table->integer('entry_coins')->nullable($value = true);
            $table->integer('coin_per_answer')->nullable($value = true);
            $table->integer('sec_to_answer')->nullable($value = true);
            $table->integer('coins_minus')->nullable($value = true);
            $table->integer('total_question_for_user')->nullable($value = true);
            $table->integer('time_break')->nullable($value = true)->comment('break time in minute');
            $table->tinyInteger('is_active')->default($value = 1);
            $table->timestamp('create_time')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->timestamp('update_time')->default(DB::raw('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'));
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('round_master');
    }
}
