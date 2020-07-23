<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->char('ip_address',16)->nullable(); // for now, will change later
            $table->string('first_name',255)->nullable(); // for now, will change later
            $table->string('last_name',255)->nullable(); // for now, will change later
            $table->string('password2',40)->nullable(); // for now, will change later
            $table->string('salt',40)->nullable(); // for now, will change later
            $table->string('email2',40)->nullable(); // for now, will change later
            $table->string('activation_code',40)->nullable(); // for now, will change later
            $table->string('forgotten_password_code',100)->nullable(); // for now, will change later
            $table->string('remember_code',40)->nullable(); // for now, will change later
            $table->integer('created_on')->nullable(); // for now, will change later
            $table->integer('last_login')->nullable(); // for now, will change 
            $table->integer('active')->nullable(); // for now, will change later
            $table->tinyInteger('details_updated')->nullable(); // for now, will change later
            $table->date('birth_date')->nullable(); // for now, will change later
            $table->enum('gender', ['m', 'f', ''])->nullable(); // for now, will change later
            $table->string('mobile_phone',22)->nullable(); // for now, will change later
            $table->string('home_phone',22)->nullable(); // for now, will change later
            $table->string('work_phone',22)->nullable(); // for now, will change later
            $table->string('picture',255)->nullable(); // for now, will change later
            $table->string('address',255)->nullable(); // for now, will change later
            $table->string('suburb',100)->nullable(); // for now, will change later
            $table->string('state',100)->nullable(); // for now, will change later
            $table->string('country',3)->nullable(); // for now, will change later
            $table->string('name');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->rememberToken();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users');
    }
}
