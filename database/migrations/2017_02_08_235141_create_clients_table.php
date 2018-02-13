<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateClientsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('clients', function (Blueprint $table) {
            
            $table->increments('id');
            $table->string('nombre_completo');
            $table->string('telefono');
            $table->string('telefono2')->nullable();
            $table->string('calle')->nullable();
            $table->string('numero')->nullable();
            $table->string('calle_e')->nullable();
            $table->string('calle_y')->nullable();
            $table->string('col')->nullable();
            $table->integer('cod_postal')->nullable();
            $table->string('rfc')->nullable();
            $table->integer('edad')->nullable();
            $table->string('ciudad')->nullable();
            $table->string('email')->nullable();
            $table->string('cercanias')->nullable();
            $table->string('referencias')->nullable();
            $table->string('documentos')->nullable();
            $table->text('comentarios')->nullable();
            $table->string('avatar')->nullable();
            $table->string('thumb')->nullable();
            $table->string('folder')->nullable();
            $table->string('situacion');
            $table->string('usuario');
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
        Schema::dropIfExists('clients');
    }
}
