<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

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
            $table->increments('id');
            $table->string('name');
            $table->string('password');
            $table->string('nombres')->nullable();
            $table->string('apellidos')->nullable();
            $table->string('email')->nullable();
            $table->string('calle')->nullable();
            $table->string('numero')->nullable();
            $table->string('col')->nullable();
            $table->string('cod_postal')->default(0);
            $table->string('device');
            $table->integer('type')->default(1);
            $table->integer('business_id')->default(0);
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
