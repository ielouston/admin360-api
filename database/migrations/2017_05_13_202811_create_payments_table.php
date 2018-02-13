<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePaymentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('abonado');
            $table->integer('saldo_anterior');
            $table->integer('saldo_actual');
            $table->integer('sale_id')->index();
            $table->integer('client_id');
            $table->integer('tipo');
            $table->string('fecha');
            $table->string('hora');
            $table->string('situacion');
            $table->string('usuario');
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
        Schema::dropIfExists('payments');
    }
}
