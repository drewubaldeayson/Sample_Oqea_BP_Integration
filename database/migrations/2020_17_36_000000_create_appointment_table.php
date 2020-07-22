<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAppointmentTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('appointments', function (Blueprint $table) {
            $table->id();
            $table->integer('recordid')->nullable();
            $table->string('patient',100)->nullable();
            $table->string('internalid',100)->nullable();
            $table->string('appointmentstartdatetime',100)->nullable();
            $table->string('appointmentenddatetime',100)->nullable();
            $table->string('appointmentlength',100)->nullable();
            $table->string('provider',100)->nullable();
            $table->string('urgent',100)->nullable();
            $table->string('appointmenttype',100)->nullable();
            $table->string('status',100)->nullable();
            $table->string('arrivaltime',100)->nullable();
            $table->string('consultationtime',100)->nullable();
            $table->string('bookedby',100)->nullable();
            $table->text('comment')->nullable();
            $table->text('itemlist')->nullable();
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
        Schema::dropIfExists('appointments');
    }
}
