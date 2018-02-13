<?php

namespace Muebleria\Http\Controllers;

use Illuminate\Http\Request;
use Muebleria\Http\Repositories\SaleRepository;
use Muebleria\Sale;
use Muebleria\Payment;
use Muebleria\Movement;
use Muebleria\Stock;
use Validator;
use Carbon\Carbon;

class SaleApiController extends Controller
{
    public function __construct()
    {
        $this->middleware('jwt.auth');
    }

    public function getByClient($id, $business_id){
        $sales = Sale::with('payments')
                ->where('client_id', $id)
                ->where('business_id', '!=' , $business_id)
                ->get();
        
        return response()->json($sales, 200);
    }
    public function getTable(Request $request)
    {
        $repo = new SaleRepository();
        $sales = null;
        $type = $request->get('type');
        
        $columns = Sale::$columnas_tabla;
        $columns_cond = Sale::$columnas_cond;
        $types = Sale::$tipos;
        $id_bussiness = $request->get('business_id');
        if($type == 'Pendiente'){
            $sales = $repo->getUnfinished($id_bussiness);
        }
        else if($type == 'Cancelada'){
            $sales = $repo->getCanceled($id_bussiness);
        }
        else{
            $sales = $repo->getByType($id_bussiness, $type);
        }
        $response = [
            'data' => $sales,
            'columnas_tabla' => $columns,
            'columnas_cond' => $columns_cond,
            'tipos' => $types
        ];
        return response()->json($response, 200);
    }

    public function getAll($business_id){
        $sales = Sale::with('payments')->where('business_id', $business_id)->get();

        return response()->json($sales, 200);
    }
    public function find($id){
        $sale = Sale::find($id);

        return response()->json($sale, 200);
    }
    public function exist($nota, $clientID, $busID){
        $sale = Sale::where([
            'nota' => $nota, 
            'client_id' => $clientID
        ])->where('situacion', '!=', 'Cancelada')
        ->where('business_id', $busID)
        ->first();
        
        if(is_null($sale)){
            return false;
        }
        return true;
    }   
    public function save(Request $request){
        
        $validator = Validator::make($request->all(),[
            'nota' => 'integer|required',
            'cliente' => 'string|required',
            'telefono' => 'string|required',
            'usuario' => 'string|required',
            'business_id' => 'integer|required'
        ]);

        $sale = new Sale();

        if($validator->fails())
        {
            return response()->json(0, 400);
        }
        
        $sale->nota = $request->get("nota");
        $sale->business_id = $request->get("business_id");
        $sale->client_id = $request->get("client_id");

        if(($this->exist($sale->nota, $sale->client_id, $sale->business_id))){
            return response()->json(0, 409);
        }

        $sale->calle = $request->get("calle");
        $sale->numero = $request->get("numero");
        $sale->colonia = $request->get("colonia");
        $sale->cod_postal = $request->get("cod_postal");
        $sale->telefono = $request->get("telefono");
        $sale->ciudad = $request->get("ciudad");
        $sale->tipo_venta = $request->get("tipo_venta");
        $sale->fecha = $request->get("fecha");
        $sale->hora = $request->get("hora");
        $sale->anticipo = $request->get("anticipo");
        $sale->descuento = $request->get("descuento");
        $sale->plazo = $request->get("plazo");
        $sale->vencimiento = $request->get("vencimiento");
        $sale->prorroga = $request->get("prorroga");
        $sale->subtotal = $request->get("subtotal");
        $sale->total = $request->get("total");
        $sale->saldo_actual = $request->get("saldo_actual");
        $sale->pagado = $request->get("pagado");
        $sale->inversion = $request->get("inversion");
        $sale->productos = $request->get("productos");
        $sale->usuario = $request->get("usuario");
        $sale->situacion = $request->get("situacion");
        $sale->salidas = $request->get("salidas");
        $sale->comentarios = $request->get("comentarios");
        $sale->cliente = $request->get("cliente");
        $sale->intereses = $request->get("intereses");

        if($sale->save()){

            $this->generateMove($sale);
            $this->updateInventory($sale);    
            
            
            if($sale->tipo_venta == "VTR"){
                $sale->device = $request->get("device");
                $sale->business_to = $request->get("business_to");
                $cBuy = new BuyApiController;
                $cBuy->traspass($sale);
            }
            return response()->json($sale->id , 200);
        }
        return response()->json(0, 500);
    }
    private function generateMove(Sale $sale){
        $mov = new Movement;
        $cMov = new MovementApiController;

        $mov->sale_id = $sale->id;
        $mov->nota = $sale->nota;
        $mov->movimiento = $sale->tipo_venta;
        $mov->entradas = $sale->tipo_venta == "VCO" ? $sale->total : $sale->anticipo;
        $mov->saldo = $sale->saldo_actual;
        $mov->usuario = $sale->usuario;
        $mov->situacion = "Completado";
        $mov->business_id = $sale->business_id;
        $mov->fecha = $cMov->parseDate($sale->fecha, 'd/m/Y', $sale->hora);
        $mov->cliente = $sale->cliente;
        
        if(!$mov->save()){
            return response()->json($mov, 500);
        }
    }
    public function regenerateMovements(Request $req){
        $sale = $req->get('sale');

        //delete the 
        Movement::where('sale_id', $sale['id'])
            ->whereIN('movimiento', [ $sale['tipo_venta'], 'INVS', 'INVSTR'])
            ->delete();

        //generate the sale movement
        $cMov = new MovementApiController;
        $mov = new Movement;
        $mov->sale_id = $sale['id'];
        $mov->cliente = $sale['cliente'];
        $mov->nota = $sale['nota'];
        $mov->movimiento = $sale['tipo_venta'];
        $mov->entradas = $sale['anticipo'];
        $mov->fecha = $cMov->parseDate($sale['fecha'], 'd/m/Y', $sale['hora']);
        $mov->usuario = $sale['usuario'];
        $mov->business_id = $sale['business_id'];
        $mov->situacion = $sale['situacion'] == "Cancelada" ? "Cancelado" : "Completado";
        if(!$mov->save()){
            return response(0, 500);
        }
        //generate the new ones
        $prods = explode(';', $sale['productos']);
        $cProduct = new ProductApiController;

        for ($i = 0; $i < count($prods); $i++)
        {
            $prod_a = explode(",", $prods[$i]);
            $prodID = $prod_a[4];
            $prod = $cProduct->getBy($prod_a[0]);
            
            $stock = Stock::where([
                    'business_id' => $sale['business_id'],
                    'product_id' => $prod->id
                ])->first();

            $exist_anterior = $stock->existencia;
            $cantidad = $prod_a[1];
            $existencia = $exist_anterior - $cantidad;
            
            //Generate product's movement
            $mov = new Movement;
            $mov->sale_id = $sale['id'];
            $mov->product_id = $stock->id;
            $mov->cliente = $prod->clave;
            $mov->nota = $sale['nota'];
            $mov->movimiento = $sale['tipo_venta'] == "VTR" ? "INVSTR" : "INVS";
            $mov->entradas = $cantidad;
            $mov->exist_anterior = $exist_anterior;
            $mov->exist_actual = $existencia;
            $mov->fecha = $cMov->parseDate($sale['fecha'], 'd/m/Y', $sale['hora']);
            $mov->usuario = $sale['usuario'];
            $mov->business_id = $sale['business_id'];

            if($sale['saldo_actual'] == 0){
                $situacion = "Completado";
            }
            else if($sale['tipo_venta'] == "VAP"){
                $situacion = "Pendiente";    
            }
            else{
                $situacion = "Completado";
            }
            
            $mov->situacion = $situacion;
            if(!$mov->save()){
                return response()->json($mov, 500);
            }
        }
        return response()->json($sale['productos'], 200);
    }
    public function updateInventory(Sale $sale){
        $prods = explode(';', $sale->productos);
        $cProduct = new ProductApiController;
        
        if($sale->tipo_venta == "VAP" && $sale->saldo_actual == 0){
            //first delete the other movs generated
            Movement::where('sale_id', $sale->id)
                    ->where('movimiento', 'INVS')
                    ->delete();
        }

        for ($i = 0; $i < count($prods); $i++)
        {
            $prod_a = explode(",", $prods[$i]);
            $prodID = $prod_a[4];
            $prod = $cProduct->getBy($prod_a[0]);
            
            $stock = Stock::where([
                    'business_id' => $sale->business_id,
                    'product_id' => $prod->id
                ])->first();

            $exist_anterior = $stock->existencia;
            $cantidad = $prod_a[1];
            $existencia = $exist_anterior - $cantidad;
            $vendidos = $stock->vendidos + $cantidad;
            $precio_compra = $prod_a[2];
            $precio_contado = $precio_compra * 2;
            $precio_mayoreo = $precio_compra + round($precio_compra * 0.2);
            $precio_oferta = $precio_compra + round($precio_compra * 0.5);

            //Generamos el movimiento
            $mov = new Movement;
            $cMov = new MovementApiController;

            $mov->sale_id = $sale->id;
            $mov->product_id = $stock->id;
            $mov->cliente = $prod->clave;
            $mov->nota = $sale->nota;
            $mov->movimiento = $sale->tipo_venta == "VTR" ? "INVSTR" : "INVS";
            $mov->entradas = $cantidad;
            $mov->exist_anterior = $exist_anterior;
            $mov->exist_actual = $existencia;
            
            $mov->usuario = $sale->usuario;
            $mov->business_id = $sale->business_id;
            $mov->fecha = $cMov->parseDate($sale->fecha, 'd/m/Y', $sale->hora);

            if($sale->saldo_actual == 0 && $sale->tipo_venta == "VAP"){
                //Update the stock
                $stock->existencia = $existencia;
                $stock->vendidos = $vendidos;
                $situacion = "Completado";
                $mov->fecha = Carbon::now();
            }
            else if($sale->tipo_venta == "VAP"){
                $situacion = "Pendiente";    
            }
            else{
                //Update the stock
                $stock->existencia = $existencia;
                $stock->vendidos += $vendidos;
                $situacion = "Completado";
            }
            
            $mov->situacion = $situacion;

            if(!$mov->save()){
                return response()->json(0, 500);
            }
            
            
            if(!$stock->update()){

                return response()->json(0, 500);
            }
        }
    }
    public function update($id, Request $request){
        
        $validator = Validator::make($request->all(),[
            'nota' => 'required|integer',
            'cliente' => 'required|string',
            'telefono' => 'required|string',
            'situacion' => 'required|string',
            'usuario' => 'string|required',
            'business_id' => 'integer|required'
        ]);
        
        $sale = Sale::find($id);

        if($validator->fails())
        {
            return response()->json(0, 400);
        }
        
        $sale->calle = $request->get("calle");
        $sale->numero = $request->get("numero");
        $sale->colonia = $request->get("colonia");
        $sale->cod_postal = $request->get("cod_postal");
        $sale->telefono = $request->get("telefono");
        $sale->ciudad = $request->get("ciudad");
        $sale->fecha = $request->get("fecha");
        $sale->anticipo = $request->get("anticipo");
        $sale->descuento = $request->get("descuento");
        $sale->plazo = $request->get("plazo");
        
        if(strlen($request->get("vencimiento") > 0)){
            $sale->vencimiento = $request->get("vencimiento");    
        }
        if(strlen($request->get("prorroga") > 0)){
            $sale->prorroga = $request->get("prorroga");    
        }

        $sale->subtotal = $request->get("subtotal");
        $sale->total = $request->get("total");
        $sale->saldo_actual = $request->get("saldo_actual");
        $sale->pagado = $request->get("pagado");
        $sale->inversion = $request->get("inversion");
        $sale->productos = $request->get("productos");
        $sale->usuario = $request->get("usuario");
        $sale->situacion = $request->get("situacion");
        $sale->salidas = $request->get("salidas");
        $sale->comentarios = $request->get("comentarios");
        $sale->cliente = $request->get("cliente");
        $sale->intereses = $request->get("intereses");

        if($sale->update()){
            return response()->json($sale->id , 200);
        }
        return response()->json(0, 500);
    }
    public function extend(Request $req){
        
        $validator = Validator::make($req->all(),[
            'nota' => 'integer|required',
            'cliente' => 'string|required',
            'telefono' => 'string|required',
            'usuario' => 'string|required',
            'business_id' => 'integer|required'
        ]);

        if($validator->fails()){
            return response()->json(0, 400);
        }
        
        $sale = Sale::find($req->get('ID'));
        $extras = $req->get('extras');
        $sale->prorroga = Carbon::now()->AddDays($extras[0]);
        $sale->saldo_actual = $sale->saldo_actual + $extras[1];
        
        if(!$sale->update()){
            return response()->json(0, 500);
        }
        
        $mov = new Movement;
        $mov->sale_id = $sale->id;
        $mov->nota = $sale->nota;
        $mov->movimiento = "VPR";
        $mov->entradas = $extras[0];
        $mov->cliente = $sale->cliente;
        $mov->fecha = Carbon::now();
        $mov->situacion = "Completado";
        $mov->usuario = $sale->usuario;
        $mov->comentarios = $extras[2];
        $mov->business_id = $sale->business_id;

        if(!$mov->save()){
            return response()->json(0, 500);
        }

        if ($extras[1] > 0)
        {
            $mov = new Movement;
            $mov->nota = $sale->nota;
            $mov->sale_id = $sale->id;
            $mov->movimiento = "VIN";
            $mov->entradas = $extras[1];
            $mov->cliente = $sale->cliente;
            $mov->saldo = $sale->saldo_actual;
            $mov->fecha = Carbon::now();
            $mov->situacion = "Completado";
            $mov->usuario = $sale->usuario;
            $mov->business_id = $sale->business_id;
            
            if(!$mov->save()){
                return response()->json(0, 500);
            }
        }
    }
    public function cancel(Request $req){

        $validator = Validator::make($req->all(),[
            'nota' => 'integer|required',
            'cliente' => 'string|required',
            'telefono' => 'string|required',
            'usuario' => 'string|required',
            'business_id' => 'integer|required'
        ]);

        if($validator->fails()){
            return response()->json(0, 400);
        }
        $sale = Sale::find($req->get('ID'));
        //Create the cancel movement
        $mov = new Movement;
        $mov->sale_id = $sale->id;
        $mov->nota = $sale->nota;
        $mov->movimiento = "VCA";
        $mov->entradas = $sale->pagado * -1;
        $mov->saldo = 0;
        $mov->cliente = $sale->cliente;
        $mov->situacion = "Completado";
        $mov->fecha = Carbon::now();
        $mov->usuario = $sale->usuario;
        $mov->business_id = $sale->business_id;
        dd($mov);
        if(!$mov->save()){
            return response()->json(0, 500);
        }

        //Cancel all the payments related to the sale
        Payment::where('sale_id', $sale->id)->update(['situacion' => 'Cancelado']);
        
        //Update the inventory if not "Apartada"
        if ($sale->situacion != "Apartada")
        {
            $prods = explode(";", $sale->productos);

            for ($i = 0; $i < count($prods); $i++)
            {
                $prod_a = explode(",", $prods[$i]);
                $cProduct = new ProductApiController;
                $prod = $cProduct->getBy($prod_a[0]);
                $stock = Stock::where([
                    'business_id' => $sale->business_id,
                    'product_id' => $prod->id
                ])->first();

                $existencia_actual = $stock->existencia + $prod_a[1];
                $existencia_anterior = $stock->existencia;

                $stock->existencia = $existencia_actual;
                
                if(!$stock->update()){
                    return response()->json(0, 500);
                }

                $mov = new Movement;
                $mov->sale_id = $sale->id;
                $mov->product_id = $stock->id;
                $mov->cliente = $prod->clave;
                $mov->nota = $sale->nota;
                $mov->movimiento = "INVEC";
                $mov->entradas = $prod_a[1];
                $mov->exist_actual = $existencia_actual;
                $mov->exist_anterior = $existencia_anterior;
                $mov->situacion = "Completado";
                $mov->fecha = Carbon::now();
                $mov->usuario = $sale->usuario;
                $mov->business_id = $sale->business_id;

                if(!$mov->save()){
                    return response()->json(0, 500);
                }
            }
        }
        //update the sale
        $sale->situacion = "Cancelada";
        $sale->salidas = 0;
        
        if(!$sale->update()){
            return response()->json(0, 500);
        }
        //Update the movements related to this sale
        $movs = array('VSA', 'INVS', 'ABNV', 'DESC', 'VIN', 'VPR', 'ACAN', 'DCAN', $sale->tipo_venta);
        Movement::whereIn('movimiento', $movs)
                ->where('sale_id', $sale->id)
                ->update(['situacion' => 'Cancelado']);
    }
    public function import(Request $request){
        $sales = json_decode($request->get('ventas'), true);
        $model = new Sale;
        $response = array();

        foreach ($sales as $sale) {
            $client_id = $sale['ID'];
            
            $validator = Validator::make($sale, [
                'nota' => 'integer|required',
                'cliente' => 'string|required',
                'telefono' => 'string|required',
                'usuario' => 'string|required',
                'client_id' => 'integer|required'
            ]);

            if($validator->fails()){
                return response()->json(0, 400);
            }
            if($this->exist($sale['nota'], $sale['client_id'], $sale['business_id'])){
                return response()->json($sale['nota'], 409);
            }
            $model = Sale::create($sale);
            
            $ids = $model->id;

            if(count($sale['payments']) > 0){
                
                foreach($sale['payments'] as $payment){
                    $pay = new Payment;
                    $pay->abonado = $payment['abonado'];
                    $pay->saldo_actual = $payment['saldo_actual'];
                    $pay->saldo_anterior = $payment['saldo_anterior'];
                    $pay->client_id = $model->client_id;
                    $pay->sale_id = $model->id;
                    $pay->fecha = $payment['fecha'];
                    $pay->hora = $payment['hora'];
                    $pay->situacion = $payment['situacion'];
                    $pay->tipo = $payment['tipo'];
                    $pay->usuario = $payment['usuario'];
                    $pay->business_id = $payment['business_id'];
                    
                    $pay->save();
                    //Set the payments id's string
                    $ids .= "|".$payment['ID'].",".$pay->id;    
                }
            }            
            $response[$client_id] = $model->id == 0 ? 0 : $ids;
        }
        return response()->json($response, 200);
    }
}
