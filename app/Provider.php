<?php

namespace Muebleria;

use Illuminate\Database\Eloquent\Model;
use Muebleria\Stock;

class Provider extends Model
{
    protected $clave;
    protected $nombre;
    protected $calle;
    protected $numero;
    protected $colonia;
    protected $cod_postal;
    protected $telefono;
    protected $telefono2;
    protected $ciudad;
    protected $situacion;
    protected $rfc;
    protected $email;
    protected $comentarios;

    protected $fillable = ['clave', 'nombre', 'calle', 'numero', 'colonia', 'cod_postal', 'telefono', 'telefono2', 'ciudad', 'situacion', 'rfc', 'email','comentarios'];

    public function stocks(){
    	return $this->hasMany(Stock::class, 'proveedor_id');
    }
}
