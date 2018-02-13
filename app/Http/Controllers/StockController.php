<?php

namespace Muebleria\Http\Controllers;
use Muebleria\Stock;
use Illuminate\Http\Request;
use Validator;

class StockController extends Controller
{
    public function update(Request $request, $id){
    	
    	$stock = Stock::find($id);

    	$validator = Validator::make($request->all(), [
    		'nombre' => 'string|required',
    		'precioCompra' => 'integer|required',
    		'stock' => 'integer|required',
    		'device' => 'string|required',
    		'business_id' => 'integer|required'
    	]);

    	if($validator->fails()){
    		return response()->json(0, 400);
    	}

        $stock->nombre = $request->get('nombre');
        $stock->descripcion = $request->get('descripcion');
        $stock->precio_compra = $request->get('precioCompra');
        $stock->precio_contado = $request->get('precioContado');
        $stock->precio_oferta = $request->get('precioOferta');
        $stock->precio_mayoreo = $request->get('precioMayoreo');
        $stock->iva = $request->get('iva');
        $stock->linea = $request->get('linea');
    	$stock->comprados = $request->get('comprados');
    	$stock->vendidos = $request->get('vendidos');
        $stock->stock = $request->get('stock');
        $stock->existencia = $request->get('existencia');

        if($stock->update()){
        	$dev = $request->get('device');
        	$business_id = $request->get('business_id');

        	$id_queue = \Event::fire('ReportMovement', array($dev, $business_id, 'stock', 'update', json_encode($stock)));
        	
            return response()->json($stock->id, 200);
        }
        return response()->json(0, 500);
    }
}
