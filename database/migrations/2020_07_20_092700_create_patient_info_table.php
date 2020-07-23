<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePatientInfoTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('patient_info', function (Blueprint $table) {
            
            $table->id();
            $table->integer('patient_id');
            $table->string('ihi',255);
            $table->string('medicare',255)->nullable();
            $table->string('medicare_expiry',10)->nullable();
            $table->string('marital_status',45)->nullable();
            $table->string('religion',100)->nullable();
            $table->string('birth_country',100)->nullable();
            $table->string('employment',255)->nullable();
            $table->string('occupation',255)->nullable();
            $table->string('name_prefix',10)->nullable();
            $table->string('health_fund',100)->nullable();
            $table->string('health_fund_membership_no',45)->nullable();
            $table->string('dva_card_no',45)->nullable();
            $table->string('dva_card_expiry',10)->nullable();
            $table->string('dva_card_type',10)->nullable();
            $table->string('pension_no',45)->nullable();
            $table->string('pension_type',10)->nullable();
            $table->string('pension_expiry',10)->nullable();
            $table->longText('next_kin')->nullable();
            $table->char('account_responsible',1)->nullable();
            $table->longText('account_responsible_info')->nullable();
            $table->dateTime('updated_at')->nullable();

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('patient_info');
    }
}
