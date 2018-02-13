<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProductsXbusinessTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('stocks', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('product_id')->index();
            $table->string('nombre');
            $table->string('descripcion');
            $table->integer('stock');
            $table->integer('existencia')->nullable()->default(0);
            $table->integer('comprados')->nullable()->default(0);
            $table->integer('vendidos')->nullable()->default(0);
            $table->integer('business_id')->index();
            $table->integer('proveedor_id')->index();
            $table->string('situacion');
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
        Schema::dropIfExists('stocks');
    }
}
