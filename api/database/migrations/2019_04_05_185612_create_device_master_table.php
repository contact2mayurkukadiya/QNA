<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDeviceMasterTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('device_master', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->integer('device_id')->autoIncrement();
            $table->unsignedInteger('user_id')->nullable($value = true);
            $table->text('device_reg_id')->nullable($value = true);
            $table->enum('device_platform',['android', 'ios', 'chrome', 'other'])->nullable($value = true);
            $table->text('device_model_name')->nullable($value = true)->comment('like: nexus 4, nexus 5');
            $table->text('device_vendor_name')->nullable($value = true)->comment('like: LG');
            $table->text('device_os_version')->nullable($value = true)->comment('like: for Android: 4.4,5.0');
            $table->text('device_udid')->nullable($value = true)->comment('udid for device');
            $table->text('device_resolution')->nullable($value = true)->comment('like: width*height');
            $table->text('device_carrier')->nullable($value = true)->comment('like: vodafone');
            $table->string('device_country_code',10)->nullable($value = true)->comment('like: +1 for us');
            $table->string('device_language',50)->nullable($value = true)->comment('like: en for english');
            $table->string('device_local_code',10)->nullable($value = true)->comment('like: 411001');
            $table->string('device_default_time_zone',25)->nullable($value = true)->comment('like: GMT+09:30');
            $table->string('device_library_version',10)->nullable($value = true)->comment('like: 1 (it is ob lib version)');
            $table->string('device_application_version',10)->nullable($value = true)->comment('Device app version');
            $table->string('device_type',25)->nullable($value = true)->comment('like: phone, tablet');
            $table->string('device_registration_date',30)->nullable($value = true)->comment('time of device when it registred');
            $table->tinyInteger('is_active')->default($value = 1)->length(1);
            $table->timestamp('create_time')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->timestamp('update_time')->default(DB::raw('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'));
            $table->foreign('user_id')->references('id')->on('user_master')->onDelete('cascade')->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('device_master');
    }
}
