<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUserMasterTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_master', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->increments('id');
            $table->string('email_id')->unique()->nullable($value = true);
            $table->string('password')->nullable($value = true);
            $table->string('social_uid')->nullable($value = true);
            $table->integer('signup_type')->nullable($value = true)->comment('1 = email, 2 = facebook, 3 = twitter 4 = gmail');
            $table->string('hash_id')->nullable($value = true);
            $table->tinyInteger('is_admin')->default($value = 0)->length(1);
            $table->tinyInteger('is_active')->default($value = 1)->length(1);
            $table->timestamp('create_time')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->timestamp('update_time')->default(DB::raw('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'));
        });

        $create_time = date('Y-m-d H:i:s');
        DB::insert('INSERT INTO user_master (id, email_id, password, social_uid, is_admin,is_active, create_time) 
                    VALUES(?,?,?,?,1, 1,?)',
            [
                1,
                "admin@gmail.com",
                '$2y$10$VlSqsW5fe8D804.JDu.tC.1FKPATAMAUIWzZT68bCQqJtFze7UXaa',
                'admin',
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
        Schema::dropIfExists('user_master');
    }
}
