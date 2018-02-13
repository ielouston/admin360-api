<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBuysTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('buys', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('nota');
            $table->integer('provider_id');
            $table->string('tipo_compra');
            $table->integer('total');
            $table->string('fecha');
            $table->string('hora');
            $table->string('situacion');
            $table->text('productos');
            $table->string('usuario');
            $table->text('comentarios');
            $table->integer('business_id')->index();
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
        Schema::dropIfExists('buys');
    }
}
