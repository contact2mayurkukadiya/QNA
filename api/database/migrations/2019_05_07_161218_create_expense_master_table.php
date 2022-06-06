<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateExpenseMasterTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('expense_master', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->integer('id')->autoIncrement();
            $table->integer('admin_user_id')->nullable($value = true)->comment('Data modification by admin');
            $table->integer('user_id')->nullable($value = true)->comment('Request by user for pay');
            $table->string('skuname')->nullable($value = true);
            $table->string('req_phone_no',15)->nullable($value = true);
            $table->tinyInteger('is_phone_no_verify')->nullable($value = true);
            $table->text('request_coin')->nullable($value = true);
            $table->text('approve_coin')->nullable($value = true);
            $table->text('payment')->nullable($value = true)->comment('actual payment');
            $table->text('pay')->nullable($value = true)->comment('payment for paid');
            $table->tinyInteger('status')->nullable($value = 0)->comment('0=Pending,1=Success,2=Return,3=Cancel');
            $table->tinyInteger('is_paid')->default($value = 0);
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
        Schema::dropIfExists('expense_master');
    }
}
