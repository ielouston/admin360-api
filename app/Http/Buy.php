<?php

namespace Muebleria;

use Illuminate\Database\Eloquent\Model;

class Buy extends Model
{
	protected $nota;
    protected $provider_id;
    protected $tipo_compra;
    protected $fecha;
    protected $hora;
    protected $total;
    protected $situacion;
    protected $productos;
    protected $usuario;
    protected $comentarios;
    protected $business_id;

    protected $fillable = [
    	'nota', 'business_id', 'provider_id', 'tipo_compra', 'total','fecha', 'hora', 'productos' ,'situacion', 'usuario', 'comentarios'
    ];

    public function movements(){
    	return $this->hasMany(Movement::class, 'buy_id');
    }
    public function provider(){
    	return $this->belongsTo(Provider::class, 'provider_id');
    }
    public function business(){
        return $this->belongsTo(Business::class, 'business_id');
    }
}
