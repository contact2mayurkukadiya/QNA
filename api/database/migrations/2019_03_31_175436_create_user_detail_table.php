<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUserDetailTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_detail', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->increments('id');
            $table->unsignedInteger('user_id');
            $table->string('first_name')->nullable($value = true);
            $table->string('last_name')->nullable($value = true)	;
            $table->string('email_id')->nullable($value = true);
            $table->string('phone_no',15)->nullable($value = true);
            $table->enum('gender',['1', '2'])->nullable($value = true);
            $table->integer('coins')->default($value = 0);
            $table->tinyInteger('is_active')->default($value = 1);
            $table->tinyInteger('is_contact')->default($value = 0);
            $table->timestamp('create_time')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->timestamp('update_time')->default(DB::raw('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'));
            $table->foreign('user_id')->references('id')->on('user_master')->onDelete('cascade')->onUpdate('cascade');

        });
        $create_time = date('Y-m-d H:i:s');
        DB::insert('INSERT INTO user_detail ( user_id, first_name, last_name, email_id,is_active,create_time) 
                    VALUES(?,?,?,?,?,?)',
            [
                1,
                'admin',
                'admin',
                'admin@gmail.com',
                1,
                $create_time
            ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('user_detail');
    }
}
