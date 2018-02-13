<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBusinessesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('businesses', function (Blueprint $table) {
            $table->increments('id');
            $table->string('nombre');
            $table->string('calle');
            $table->string('numero');
            $table->string('colonia');
            $table->string('cod_postal');
            $table->string('rfc');
            $table->string('ciudad');
            $table->string('estado');
            $table->string('telefonos');
            $table->integer('usuario_id')->index();
            $table->string('avatar')->nullable();
            $table->string('tipo');
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
        Schema::dropIfExists('businesses');
    }
}
