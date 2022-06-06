<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRolesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('roles', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->increments('id');
            $table->string('name')->nullable($value = true);
            $table->string('display_name',255)->nullable($value = true);
            $table->string('description',255)->nullable($value = true);
            $table->timestamp('create_time')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->timestamp('update_time')->default(DB::raw('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'));
        });
        $create_time = date('Y-m-d H:i:s');
        DB::beginTransaction();
        DB::insert('INSERT INTO roles (name, display_name, description) VALUES (?,?,?)',[
           'admin',
           'admin',
            $create_time
        ]);
        $create_time1 = date('Y-m-d H:i:s');
        DB::insert('INSERT INTO roles (name, display_name, description) VALUES (?,?,?)',[
            'user',
            'user',
            $create_time1
        ]);
        DB::commit();

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('roles');
    }
}
