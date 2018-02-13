<?php

namespace Muebleria;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    protected $abonado;
    protected $saldo_actual;
    protected $saldo_anterior;
    protected $client_id;
    protected $sale_id;
    protected $fecha;
    protected $hora;
    protected $situacion;
    protected $tipo;
    protected $usuario;
    protected $business_id;

    protected $fillable = [
    	'abonado', 'saldo_actual', 'saldo_anterior', 'client_id', 'sale_id', 'business_id','fecha', 'hora', 'situacion', 'tipo', 'usuario'
    ];

    public function sale(){
    	return $this->belongsTo(Sale::class, 'sale_id');
    }
}
