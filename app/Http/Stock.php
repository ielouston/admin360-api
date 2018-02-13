<?php

namespace Muebleria;

use Illuminate\Database\Eloquent\Model;

class Stock extends Model
{
    protected $nombre;
    protected $descripcion;
    protected $existencia;
    protected $stock;
    protected $proveedor_id;
    protected $comprados;
    protected $vendidos;
    protected $product_id;
    protected $situacion;
    protected $business_id;
    
    protected $fillable = [
    	'nombre', 'descripcion', 'existencia', 'stock', 'proveedor_id', 'comprados', 'vendidos', 'product_id', 'situacion', 'business_id'
    ];

    public function product(){
        return $this->belongsTo(Product::class, 'product_id');
    }
    public function business(){
        return $this->belongsTo(Business::class, 'business_id');
    }
    public function provider(){
        return $this->belongsTo(Provider::class, 'proveedor_id');
    }
}
