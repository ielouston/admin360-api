<?php

namespace Muebleria;

use Illuminate\Database\Eloquent\Model;

class Inventory extends Model
{
    protected $productos;
    protected $no_productos;
    protected $total_entradas; 
    protected $total_salidas;
    protected $usuario;
    protected $comentarios;
    protected $business_id;

    protected $fillable = [
    	'productos', 'no_productos', 'total_entradas', 'total_salidas', 'usuario', 'comentarios', 'business_id'
    ];
}
