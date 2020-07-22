<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePatientTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('patients', function (Blueprint $table) {
            $table->id();
            $table->integer('internal_id')->nullable();
            $table->string('patient_name',100)->nullable();
            $table->string('address',100)->nullable();
            $table->string('dob',100)->nullable();
            $table->string('sex',100)->nullable();
            $table->string('ethnicity',100)->nullable();
            $table->string('home_phone',100)->nullable();
            $table->string('work_phone',100)->nullable();
            $table->string('mobile_phone',100)->nullable();
            $table->string('email',100)->nullable();
            $table->string('medicare_no',100)->nullable();
            $table->string('pension_no',100)->nullable();
            $table->string('religion',100)->nullable();
            $table->string('usual_doctor',100)->nullable();
            $table->string('record_status',100)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('patients');
    }
}
