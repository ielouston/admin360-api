<?php

namespace Muebleria;

use Illuminate\Database\Eloquent\Model;

class Movement extends Model
{
    protected $sale_id;
    protected $product_id;
    protected $buy_id;
    protected $payment_id;
    protected $expense_id;
    protected $inventory_id;
    protected $exist_anterior;
    protected $exist_actual;
    protected $nota;
    protected $cliente;
    protected $movimiento;
    protected $entradas;
    protected $salidas;
    protected $saldo;
    protected $fecha;
    protected $situacion;
    protected $usuario;
    protected $comentarios;
    protected $business_id;

    protected $fillable = [
    	'sale_id', 'product_id', 'buy_id', 'payment_id', 'expense_id', 'inventory_id', 'exist_anterior', 'exist_actual','nota', 'cliente', 'movimiento', 'entradas', 'salidas', 'saldo', 'fecha', 'situacion', 'usuario','comentarios', 'business_id'
    ];

    public function scopeByDates($query, $date_from, $date_to, $both = false, $business_id = 0){
        if($both == "true"){
            if (strlen($date_from) > 0 && $both){
                $query->where('fecha', '>', $date_from);
                $query->orWhere('fecha', 'like', $date_from .'%');
            }
            if(strlen($date_to) > 0 && $both){
                $query->where('fecha', '<=', $date_to);
            }
        }
        else { 
            $query->where('fecha', 'like', $date_from . '%');   
        }
        if($business_id > 0){
            $query->where('business_id', $business_id);
        }
        $query->orderBy('fecha', 'ASC');
        return $query;
    }
    public function sale(){
    	return $this->belongsTo(Sale::class, 'sale_id');
    }
    public function product(){
    	return $this->belongsTo(Stock::class, 'product_id');
    }
    public function payment(){
    	return $this->belongsTo(Payment::class, 'payment_id');
    }
}
