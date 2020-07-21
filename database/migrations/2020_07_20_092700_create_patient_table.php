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
            $table->string('patient_name',100);
            $table->string('address',100);
            $table->string('dob',100);
            $table->string('sex',100);
            $table->string('ethnicity',100);
            $table->string('home_phone',100);
            $table->string('work_phone',100);
            $table->string('mobile_phone',100);
            $table->string('email',100);
            $table->string('medicare_no',100);
            $table->string('pension_no',100);
            $table->string('religion',100);
            $table->string('usual_doctor',100);
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
