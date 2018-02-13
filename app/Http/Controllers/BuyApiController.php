<?php

namespace Muebleria\Http\Controllers;

use Illuminate\Http\Request;
use Validator;
use Muebleria\Buy;
use Muebleria\Business;
use Muebleria\Movement;
use Carbon\Carbon;
use Muebleria\Provider;
use Muebleria\Stock;
use Muebleria\Sale;
use Muebleria\Queue;

class BuyApiController extends Controller
{
	public function __construct(){
		$this->middleware('jwt.auth');
	}

    public function find($id){
        $buy = Buy::find($id);

        return response()->json($buy, 200);
    }
    
    public function store(Request $req){
    	
    	$validator = Validator::make($req->all(), [
    		'nota' => 'integer|required',
    		'provider_id' => 'integer|required',
    		'tipo_compra' => 'string|required',
    		'situacion' => 'string|required',
    		'fecha' => 'string|required',
    		'hora' => 'string|required',
    		'usuario' => 'string|required',
    		'productos' => 'string|required'
    	]);

    	if($validator->fails()){
    		return response()->json(0, 400);
    	}

    	$buy = new Buy;
    	$buy->nota = $req->get('nota');
    	$buy->provider_id = $req->get('provider_id');
    	$buy->tipo_compra = $req->get('tipo_compra');
        $buy->total = $req->get('total');
    	$buy->situacion = $req->get('situacion');
    	$buy->fecha = $req->get('fecha');
    	$buy->hora = $req->get('hora');
    	$buy->usuario = $req->get('usuario');
    	$buy->productos = $req->get('productos');
    	$buy->comentarios = $req->get('comentarios');
        $buy->business_id = $req->get('business_id');
        

    	if($buy->save()){
    		$buy->proveedor = $req->get('clave');
            $this->generateMove($buy);
            $this->updateInventory($buy);

    		return response()->json($buy->id, 200);
    	}
    	return response()->json(0, 500);
    }
    private function generateMove(Buy $buy){

        $mov = new Movement;
        $cMov = new MovementApiController;

        $mov->buy_id = $buy->id;
        $mov->nota = $buy->nota;
        $mov->movimiento = $buy->tipo_compra;
        $mov->situacion = "Completado";
        $mov->fecha = $cMov->parseDate($buy->fecha, 'd/m/Y', $buy->hora);
        $mov->entradas = $buy->total;
        $mov->saldo = 0;
        $mov->cliente = $buy->proveedor;
        $mov->usuario = $buy->usuario;
        $mov->business_id = $buy->business_id;

        if(!$mov->save()){
            return response()->json($mov, 500);
        }
    }
    private function generateQueue(Buy $buy, $action, $device){
        $queue = new Queue();
        $queue->model = "buy";
        $queue->action = $action;
        $queue->data = json_encode($buy);
        $queue->devices = $device;
        $queue->business_id = $buy->business_id;
        $queue->status = "Creado";

        if(!$queue->save()){
            return response()->json($queue, 500);
        }
    }
    private function updateInventory(Buy $buy){

        $prods = explode(";", $buy->productos);
        $cProduct = new ProductApiController;

        for ($i = 0; $i < count($prods); $i++)
        {
            $prod_a = explode(",", $prods[$i]);
            $prodID = $prod_a[4];
            $prod = $cProduct->getBy($prod_a[0]);
            $stock = Stock::firstOrCreate([
                        'business_id' => $buy->business_id,
                        'product_id' => $prod->id
                    ],[
                        'business_id' => $buy->business_id,
                        'product_id' => $prod->id,
                        'situacion' => 'Activo',
                        'stock' => 1,
                        'nombre' => $prod->nombre,
                        'descripcion' => $prod->descripcion,
                        'comprados' => 0,
                        'vendidos' => 0,
                        'existencia' => 0,
                        'proveedor_id' => 1
            ]);

            $exist_anterior = $stock->existencia;
            $cantidad = $prod_a[1];
            $existencia = $exist_anterior + $cantidad;
            $comprados = $stock->comprados + $cantidad;
            $precio_compra = $prod_a[2];
            $precio_contado = $precio_compra * 2;
            $precio_mayoreo = $precio_compra + round($precio_compra * 0.2);
            $precio_oferta = $precio_compra + round($precio_compra * 0.5);

            $prod->precio_compra = $precio_compra;
            $prod->precio_contado = $precio_contado;
            $prod->precio_mayoreo = $precio_mayoreo;
            $prod->precio_oferta = $precio_oferta;

            if(!$prod->update()){
                return response()->json(0, 500);
            }

            //Generamos el movimiento
            $mov = new Movement;
            $cMov = new MovementApiController;

            $mov->buy_id = $buy->id;
            $mov->product_id = $stock->id;
            $mov->cliente = $prod->clave;
            $mov->nota = $buy->nota;
            $mov->movimiento = $buy->situacion == "Cancelada" ? "INVSC": "INVE";
            $mov->entradas = $cantidad;
            $mov->exist_anterior = $exist_anterior;
            $mov->exist_actual = $existencia;
            $mov->fecha = $cMov->parseDate($buy->fecha, 'd/m/Y', $buy->hora);
            $mov->situacion = "Completado";
            $mov->usuario = $buy->usuario;
            $mov->business_id = $buy->business_id;

            if(!$mov->save()){
                return response()->json($mov, 500);
            }
            //Actualizamos el stock
            $stock->existencia = $existencia;
            $stock->comprados = $comprados;
            
            if(!$stock->update()){
                return response()->json(0, 500);
            }
        }
    }
    public function traspass(Sale $sale){
        $buy = new Buy;
        $buy->nota = $sale->nota;
        $buy->provider_id = 1;
        $buy->tipo_compra = "CTR";
        $buy->total = 0;
        $buy->situacion = "Saldada";
        $buy->fecha = $sale->fecha;
        $buy->hora = $sale->hora;
        $buy->usuario = $sale->usuario;
        $buy->productos = $sale->productos;
        $buy->comentarios = $sale->comentarios;
        $buy->business_id = $sale->business_to;

        if($buy->save()){
            $bus = Business::find($sale->business_id);
        	$buy->proveedor = $bus->nombre;
            $this->generateMove($buy);
            $this->updateInventory($buy);
            $this->generateQueue($buy, "create", $sale->device);

            return response()->json($buy->id, 200);
        }
    }
    public function cancel(Request $req){
        
        $validator = Validator::make($req->all(), [
            'nota' => 'integer|required',
            'provider_id' => 'integer|required',
            'tipo_compra' => 'string|required',
            'situacion' => 'string|required',
            'usuario' => 'string|required',
            'business_id' => 'integer|required'
        ]);

        if($validator->fails()){
            return response()->json(0, 400);
        }
        $cProduct = new ProductApiController;
        $id = $req->get('ID');
        $buy = Buy::find($id);
        $buy->situacion = "Cancelada";
        
        if(!$buy->update()){
            return response()->json($buy, 500);
        }

        Movement::where('buy_id', $buy->id)->update(['situacion' => 'Cancelado']);

        $mov = new Movement;
        $mov->buy_id = $buy->id;
        $mov->nota = $buy->nota;
        $mov->movimiento = "CCA";
        $mov->entradas = 0;
        $mov->saldo = 0;
        $provider = Provider::find($buy->provider_id);
        $mov->cliente = $provider->clave;
        $mov->situacion = "Completado";
        $mov->fecha = Carbon::now();
        $mov->usuario = $buy->usuario;
        $mov->business_id = $buy->business_id;
        
        if(!$mov->save()){
            return response()->json($mov, 500);
        }

        $prods = explode(";", $buy->productos);

        for ($i = 0; $i < count($prods); $i++)
        {
            $prod_a = explode(",", $prods[$i]);
            $prod = $cProduct->getBy($prod_a[0]);
            $stock = Stock::where([
                        'business_id' => $buy->business_id,
                        'product_id' => $prod->id
                    ])->first();
            $cant = $prod_a[1];
            $existencia_actual = $stock->existencia - $cant;
            $existencia_anterior = $stock->existencia;
            $stock->existencia = $existencia_actual;
            
            if(!$stock->update()){
                return response()->json($aux, 500);
            }

            $mov = new Movement;
            $mov->buy_id = $buy->id;
            $mov->product_id = $stock->id;
            $mov->nota = $buy->nota;
            $mov->movimiento = "INVSC";
            $mov->entradas = $cant;
            $mov->cliente = $prod->clave;
            $mov->exist_actual = $stock->existencia;
            $mov->exist_anterior = $existencia_anterior;
            $mov->situacion = "Completado";
            $mov->fecha = Carbon::now();
            $mov->usuario = $buy->usuario;
            $mov->business_id = $stock->business_id;

            if(!$mov->save()){
                return response()->json($mov, 500);
            }
        }

        if($buy->save()){
            return response()->json($buy->id, 200);
        }
        return response()->json(0, 500);
    }
    public function import(Request $req){
        $validator = Validator::make($req->all(), [
            'compras' => 'required',
            'business_id' => 'integer|required'
        ]);

        if($validator->fails()){
            return response()->json(0, 400);
        }

        $buys = json_decode($req->get('compras'), true);
        $response = array();

        foreach($buys as $buy){
            $client_id = $buy['ID'];

            $buy['business_id'] = $req->get('business_id');
            $model = Buy::create($buy);
            $response[$client_id] = $model->id;
        }
        return response()->json($response, 200);
    }
    public function getAll($business_id){
    	$buys = Buy::where('business_id', $business_id)->get();

    	return response()->json($buys, 200);
    }
}
