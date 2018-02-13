<?php

namespace Muebleria\Http\Controllers;

use Illuminate\Http\Request;
use Validator;
use Muebleria\Payment;
use Muebleria\Movement;
use Muebleria\Sale;
use Carbon\Carbon;

class PaymentController extends Controller
{
    public function store(Request $req){
    	
    	$validator = Validator::make($req->all(), [
    		'abonado' => 'integer|required',
    		'saldo_anterior' => 'integer|required',
    		'saldo_actual' => 'integer|required',
    		'sale_id' => 'integer|required',
    		'usuario' => 'string|required',
            'business_id' => 'integer|required'
    	]);

    	if($validator->fails() || $this->payedSale($req->get('sale_id'))){
    		return response()->json(0, 400);
    	}

    	$pay = new Payment;
    	$pay->abonado = $req->get('abonado');
    	$pay->saldo_actual = $req->get('saldo_actual');
    	$pay->saldo_anterior = $req->get('saldo_anterior');
    	$pay->sale_id = $req->get('sale_id');
        $sale = Sale::find($pay->sale_id);
    	$pay->client_id = $sale->client_id;
    	$pay->fecha = $req->get('fecha');
    	$pay->hora = $req->get('hora');
    	$pay->tipo = $req->get('tipo');
    	$pay->usuario = $req->get('usuario');
    	$pay->situacion = "Pagado";
        $pay->business_id = $req->get('business_id');

    	if($pay->save()){
            $this->generateMove($pay);
            
    		return response()->json($pay->id, 200);
    	}
    	return response()->json(0, 500);
    }
    private function generateMove(Payment $pay){
        $mov = new Movement;
        $cMov = new MovementApiController;
        $mov->payment_id = $pay->id;
        $mov->sale_id = $pay->sale_id;
        $mov->entradas = $pay->abonado;
        $mov->movimiento = $pay->tipo == 0 ? "ABNV" : "DESC";
        $mov->saldo = $pay->saldo_actual;
        $sale = Sale::find($pay->sale_id);
        $mov->nota = $sale->nota;
        $mov->cliente = $sale->cliente;
        $mov->situacion = "Completado";
        $mov->usuario = $pay->usuario;
        $mov->fecha = $cMov->parseDate($pay->fecha, 'd/m/Y', $pay->hora);
        $mov->business_id = $pay->business_id;
        
        if($mov->save()){
            $this->updateSale($pay, $sale);
        }
        return response()->json(0, 500);
    }
    private function updateSale($pay, $sale){
        if ($pay->tipo == 0)
        {
            $sale->pagado += $pay->abonado;
            $sale->vencimiento = Carbon::now()->addDays(7);
            $sale->prorroga = $sale->vencimiento;
        }
        else
        {
            $sale->total -= $pay->abonado;
            $sale->descuento += $pay->abonado;
        }
        $sale->saldo_actual = $pay->saldo_actual;
        if ($pay->saldo_actual == 0)
        {
            $sale->situacion = "Saldada";
            $cSale = new SaleApiController;
            if ($sale->tipo_venta == "VAP") { $cSale->updateInventory($sale); }
        }
        if(!$sale->update()){
            return response()->json(0, 500);
        }
    }
    public function update(){
    	$validator = Validator::make($req->all(), [
    		'abonado' => 'integer|required',
    		'saldo_anterior' => 'integer|required',
    		'saldo_actual' => 'integer|required',
    		'sale_id' => 'integer|required',
    		'client_id' => 'integer|required',
    		'usuario' => 'string|required',
    		'situacion' => 'string|required'
    	]);

    	if($validator->fails() || $this->payedSale($req->get('sale_id'))){
    		return response()->json(0, 400);
    	}

    	$pay = new Payment;
    	$pay->abonado = $req->get('abonado');
    	$pay->saldo_actual = $req->get('saldo_actual');
    	$pay->saldo_anterior = $req->get('saldo_anterior');
    	$pay->sale_id = $req->get('sale_id');
    	$pay->client_id = $req->get('client_id');
    	$pay->fecha = $req->get('fecha');
    	$pay->hora = $req->get('hora');
    	$pay->tipo = $req->get('tipo');
    	$pay->usuario = $req->get('usuario');
    	$pay->situacion = $req->get('situacion');

    	if($pay->save()){
    		return response()->json($pay->id, 200);
    	}
    	return response()->json(0, 500);
    }
    public function cancel(Request $req){
        
        $validator = Validator::make($req->all(), [
            'abonado' => 'integer|required',
            'saldo_anterior' => 'integer|required',
            'saldo_actual' => 'integer|required',
            'sale_id' => 'integer|required',
            'client_id' => 'integer|required',
            'usuario' => 'string|required'
        ]);

        if($validator->fails()){
            return response()->json(0, 400);
        }
        $pay = Payment::find($req->get('ID'));
        $salida = $pay->abonado * -1;
        $sale = Sale::find($pay->sale_id);
        $pay->situacion = "Cancelado";
        $cMov = new MovementApiController;
        $cMov->cancelBy("payment_id", $pay->id);

        $mov = new Movement;
        $mov->payment_id = $pay->id;
        $mov->sale_id = $sale->id;
        $mov->movimiento = $pay->tipo == 0 ? "ACAN" : "DCAN";
        $mov->nota = $sale->nota;
        $mov->entradas = $salida;
        $mov->situacion = "Completado";
        $mov->fecha = Carbon::now();
        $mov->usuario = $sale->usuario;
        $mov->cliente = $sale->cliente;
        $mov->business_id = $sale->business_id;

        if(!$mov->save() || !$pay->update()){
            return response()->json(0, 500);
        }

        if ($pay->tipo == 0)
        {
            $sale->pagado -= $pay->abonado;
            $sale->saldo_actual += $pay->abonado;
        }
        else
        {
            $sale->total += $pay->abonado;
            $sale->descuento -= $pay->abonado;
        }

        if(!$sale->update()){
            return response()->json(0, 500);    
        }
    }
    public function payedSale($id){
    	$sale = Sale::find($id);
    	if($sale->situacion == "Saldada" || $sale->situacion == "Cancelada"){
    		return true;
    	}
    	return false;
    }
    public function getAll($business_id){
        $payments = Payment::where('business_id', $business_id)->get();

        return response()->json($payments, 200);
    }
    private function getDateFromMove($id, $movimiento){
        $mov = Movement::where('payment_id', $id)
                        ->where('movimiento', $movimiento)
                        ->first();
        return $mov->fecha;
    }
}
