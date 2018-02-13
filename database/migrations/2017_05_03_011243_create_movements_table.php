<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMovementsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('movements', function(Blueprint $table){
            $table->increments('id');
            $table->integer('sale_id')->default(0);
            $table->integer('product_id')->default(0);
            $table->integer('buy_id')->default(0);
            $table->integer('payment_id')->default(0);
            $table->integer('expense_id')->default(0);
            $table->integer('inventory_id')->default(0);
            $table->integer('exist_anterior')->default(0);
            $table->integer('exist_actual')->default(0);
            $table->integer('nota')->default(0);
            $table->string('movimiento');
            $table->integer('entradas')->default(0);
            $table->integer('salidas')->default(0);
            $table->integer('saldo')->default(0);
            $table->string('fecha');
            $table->string('situacion');
            $table->string('cliente')->nullable();
            $table->text('comentarios')->nullable();
            $table->integer('business_id')->index();
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
        Schema::dropIfExists('movements');
    }
}
