<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProvidersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('providers', function (Blueprint $table) {
            $table->increments('id');
            $table->string('clave')->unique();
            $table->string('nombre');
            $table->string('calle')->default('0')->default('0');
            $table->string('numero')->default('0');
            $table->string('colonia')->default('0');
            $table->string('cod_postal')->nullable();
            $table->string('telefono')->default('0');
            $table->string('telefono2')->nullable();
            $table->string('ciudad')->nullable();
            $table->string('rfc')->nullable();
            $table->string('email')->nullable();
            $table->text('comentarios')->nullable();
            $table->string('situacion')->default('Activo');
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
        Schema::dropIfExists('providers');
    }
}
