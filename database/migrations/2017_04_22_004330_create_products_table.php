<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('products', function (Blueprint $table) {
            $table->increments('id');
            $table->string('clave')->unique();
            $table->string('claves_aux');
            $table->string('nombre');
            $table->string('descripcion');
            $table->string('avatar')->nullable();
            $table->string('thumb')->nullable();
            $table->integer('precio_compra');
            $table->integer('precio_contado');
            $table->integer('precio_oferta');
            $table->integer('precio_mayoreo');
            $table->boolean('oferta')->default(false);
            $table->integer('descuento');
            $table->integer('iva');
            $table->string('linea')->nullable();
            $table->string('situacion')->default("Activo");
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
        Schema::dropIfExists('products');
    }
}
