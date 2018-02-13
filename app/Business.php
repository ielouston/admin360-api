<?php

namespace Muebleria;

use Illuminate\Database\Eloquent\Model;

class Business extends Model
{
    protected $nombre;
    protected $direccion;
    protected $telefonos;
    protected $tipo;
    protected $usuario_id;

    protected $fillable = ['nombre', 'direccion', 'tipo', 'telefonos', 'usuario_id'];
    
    public function scopeVirtuals($query){
        return $query->where('businesses.tipo', 'Virtual');
    }
    public function scopePhysicals($query){
        return $query->whereIn('businesses.tipo', ['Sucursal', 'Matriz']);
    }
    public function movements() {
        return $this->hasMany(Movement::class, 'business_id');
    }
    public function sales(){
    	return $this->hasMany(Sale::class, 'business_id');
    }
    public function users(){
    	return $this->belongsTo(User::class, 'id');
    }
    public function stocks(){
    	return $this->hasMany(Stock::class, 'business_id');
    }
}
