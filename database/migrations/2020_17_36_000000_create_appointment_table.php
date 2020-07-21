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
            $table->string('patient',100);
            $table->string('internalid',100);
            $table->string('appointmentstartdatetime',100);
            $table->string('appointmentenddatetime',100);
            $table->string('appointmentlength',100);
            $table->string('provider',100);
            $table->string('urgent',100);
            $table->string('appointmenttype',100);
            $table->string('status',100);
            $table->string('arrivaltime',100);
            $table->string('consultationtime',100);
            $table->string('bookedby',100);
            $table->string('comment',100);
            $table->string('itemlist',100);
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
