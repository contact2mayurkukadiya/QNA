<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDebitMasterTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('debit_master', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->integer('id')->autoIncrement();
            $table->integer('user_id')->nullable($value = true)->comment('Data modification by admin');
            $table->text('expenses_no')->nullable($value = true);
            $table->text('expenses_name')->nullable($value = true);
            $table->integer('expenses_price')->nullable($value = true);
            $table->double('trans_per')->nullable($value = true);
            $table->integer('coins')->nullable($value = true);
            $table->double('amount')->nullable($value = true);
            $table->double('invite_amt')->nullable($value = true);
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
        Schema::dropIfExists('debit_master');
    }
}
