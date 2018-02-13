<?php

namespace Muebleria\Http\Controllers;

use Illuminate\Http\Request;
use Muebleria\Inventory;
use Muebleria\Movement;
use Muebleria\Stock;
use Muebleria\Product;
use Muebleria\Http\Controllers\ProductApiController;
use Validator;
use Carbon\Carbon;
 
class InventoryController extends Controller
{
    public function store(Request $req){
    	
    	$validator = Validator::make($req->all(), [
    		'productos' => 'string|required',
    		'no_productos' => 'integer|required',
    		'total_entradas' => 'integer|required',
    		'total_salidas' => 'integer|required',
    		'usuario' => 'string|required',
            'business_id' => 'integer|required'
    	]);

    	if($validator->fails()){
    		return response()->json($validator, 400);
    	}

    	$inv = new Inventory;
    	$inv->productos = $req->get('productos');
    	$inv->no_productos = $req->get('no_productos');
    	$inv->total_entradas = $req->get('total_entradas');
    	$inv->total_salidas = $req->get('total_salidas');
    	$inv->usuario = $req->get('usuario');
    	$inv->comentarios = $req->get('comentarios');
    	$inv->business_id = $req->get('business_id');

    	if($inv->save()){
            $this->generateMove($inv);
            $this->updateInventory($inv);
    		return response()->json($inv->id, 200);
    	}
    	return response()->json($inv , 500);
    }

    public function import(Request $req){
        
        $validator = Validator::make($req->all(), [
            'inventarios' => 'required',
            'business_id' => 'integer|required'
        ]);

        if($validator->fails()){
            return response()->json(0, 400);
        }

        $invs = json_decode($req->get('inventarios'), true);
        $model = new Inventory;
        $response = array();

        foreach ($invs as $inv) {
            $client_id = $inv['ID'];
            
            $validator = Validator::make($req->all(), [
                'productos' => 'string|required',
                'no_productos' => 'integer|required',
                'total_entradas' => 'integer|required',
                'total_salidas' => 'integer|required',
                'usuario' => 'string|required'
            ]);

            if($validator->fails()){
                $model->id = 0;
            }
            $inv['business_id'] = $req->get('business_id');
            $model = Inventory::create($inv);
            $response[$client_id] = $model->id == 0 ? 0 : $model->id;
        }
        return response()->json($response, 200);
    }
    public function getAll($business_id){
    	$invs = Inventory::where('business_id', $business_id)->get();

    	return response()->json($invs, 200);
    }
    public function generateMove(Inventory $inv){
        $mov = new Movement;
        $mov->inventory_id = $inv->id;
        $mov->movimiento = "INVN";
        $mov->entradas = $inv->no_productos;
        $mov->situacion = "Completado";
        $mov->usuario = $inv->usuario;
        $mov->fecha = $inv->created_at;
        $mov->business_id = $inv->business_id;

        if(!$mov->save()){
            return response()->json($mov, 500);
        }
    }
/*
    *   Method for update the product's stocks by the Inventory
 */

    private function updateInventory(Inventory $inv){
        
        $prod = new Product;
        $prods = explode('|', $inv->productos);
        $cProd = new ProductApiController;

        for ($i = 0; $i < count($prods); $i++)
        {
            $prod_a = explode(";", $prods[$i]);
            $exist_anterior = $prod_a[0];
            $exist_actual = $prod_a[1];
            $diferencia = $prod_a[2];
            $prodID = $prod_a[3];
            $clave = $prod_a[4];
            $movimiento = $diferencia > 0 ? "INVEI" : "INVSI";
            
            $prod = $cProd->getBy($clave);
            $stock = Stock::firstOrCreate([
                'business_id' => $inv->business_id,
                'product_id' => $prod->id
            ], [
                'nombre' => $prod->nombre,
                'descripcion' => $prod->descripcion,
                'existencia' => 0,
                'stock' => 1,
                'proveedor_id' => 1,
                'comprados' => 0,
                'vendidos' => 0,
                'situacion' => 'Activo',
                'business_id' => $inv->business_id,
                'product_id' => $prod->id
            ]);

            $stock->existencia = $exist_actual;

            if($stock->update()){

                $mov = new Movement;
                $mov->cliente = $prod->clave;
                $mov->movimiento = $movimiento;
                $mov->entradas = $diferencia;
                $mov->exist_anterior = $exist_anterior;
                $mov->exist_actual = $exist_actual;
                $mov->product_id = $prod->id;
                $mov->inventory_id = $inv->id;
                $mov->fecha = Carbon::now();
                $mov->usuario = $inv->usuario;
                $mov->situacion = "Completado";
                $mov->business_id = $inv->business_id;
                
                if(!$mov->save()){
                    return response()->json($mov, 500);
                }
            }
        }
    }
}
