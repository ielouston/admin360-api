<?php

namespace Muebleria;
 
use Illuminate\Database\Eloquent\Model;
use Muebleria\Scopes\SearchPaginateAndOrder;

class Product extends Model
{
    protected $clave;
    protected $claves_aux;
    protected $nombre;
    protected $descripcion;
    protected $precio_compra;
    protected $precio_contado;
    protected $precio_oferta;
    protected $precio_mayoreo;
    protected $iva;
    protected $linea;
    protected $avatar;
    protected $thumb;
    protected $descuento;
    protected $oferta;

    protected $fillable = [
    	'clave', 'claves_aux','nombre', 'descripcion', 'precio_compra', 'precio_contado', 'precio_oferta', 'precio_mayoreo', 'iva', 'linea', 'avatar', 'thumb', 'descuento', 'oferta'
    ];

    public static $table_name = "products";
    public static $columnas_tabla = ['clave', 'nombre','descripcion', 'precio_compra','precio_contado', 'precio_oferta', 'precio_mayoreo', 'linea', 'updated_at'];

    public static $columnas_cond = ['clave', 'nombre', 'linea', 'precio_compra', 'precio_contado', 'precio_mayoreo', 'precio_oferta', 'updated_at'];

    public static $tipos = ['Activos', 'Inhabilitados'];
    
    protected static function boot(){
        parent::boot();
        static::addGlobalScope(new SearchPaginateAndOrder);
    }
    public function scopeQueryByWord($query, $name = ""){
        return $query->where('products.nombre', 'like', '%'. $name . '%');
    }
    public function scopeByCategory($query, $category = ""){
        return $query->where('products.linea', $category);
    }
    public function scopeActive($query){
        return $query->where('products.situacion', 'Activo');
    }
    public function scopeInactive($query){
        return $query->where('products.situacion', 'Inactivo');
    }
    public function scopeInOffer($query){
        return $query->where('products.oferta', '1');
    }
    public function stocks(){
        return $this->hasMany(Stock::class, 'product_id');
    }
    public function stock($business_id){
        return $this->hasOne(Stock::class, 'product_id')->where('business_id', $business_id);
    }
    public function categories(){
        return $this->selectRaw('distinct(linea) as categorias');
    }
    public function stockSells(){
        return $this->leftJoin()
    }
}
