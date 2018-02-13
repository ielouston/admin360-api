<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSalesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sales', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('nota');
            $table->integer('client_id')->index();
            $table->integer('business_id')->index();
            $table->string('calle');
            $table->string('numero')->default(0);
            $table->string('colonia')->default(0);
            $table->integer('cod_postal');
            $table->string('telefono')->default(0);
            $table->string('ciudad')->default(0);
            $table->string('tipo_venta');
            $table->string('fecha');
            $table->string('hora');
            $table->integer('anticipo');
            $table->integer('descuento');
            $table->string('plazo')->nullable();
            $table->string('vencimiento');
            $table->string('prorroga');
            $table->integer('subtotal');
            $table->integer('total');
            $table->integer('saldo_actual');
            $table->integer('pagado');
            $table->integer('inversion');
            $table->text('productos');
            $table->string('usuario');
            $table->string('situacion');
            $table->text('comentarios')->nullable();
            $table->string('cliente');
            $table->integer('intereses')->default(0);
            $table->integer('salidas')->default(0);
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
        Schema::dropIfExists('sales');
    }
}
