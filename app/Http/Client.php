<?php

namespace Muebleria;

use Illuminate\Database\Eloquent\Model;
use Muebleria\Client;
use Illuminate\Http\Request;
use Muebleria\Scopes\SearchPaginateAndOrder;

class Client extends Model
{
	protected $nombre_completo;
	protected $email;
	protected $telefono;
	protected $telefono2;
	protected $edad;
	protected $calle;
	protected $numero;
	protected $calle_e;
	protected $calle_y;
	protected $col;
	protected $cod_postal;
	protected $ciudad;
	protected $cercanias;
	protected $referencias;
	protected $documentos;
	protected $comentarios;
	protected $avatar;
    protected $thumb;
	protected $folder;
	protected $situacion;
	protected $usuario;
    public static $table_name = 'clients';

    protected $fillable = ['nombre_completo', 'email', 'telefono', 'telefono2', 'edad', 'calle', 'numero', 'calle_e', 'calle_y', 'col', 'cod_postal', 'ciudad', 'cercanias', 'referencias', 'documentos', 'comentarios', 'avatar', 'folder', 'situacion', 'usuario'];

    public static $columnas_tabla = ['nombre_completo', 'telefono','calle', 'numero','col', 'ciudad', 'created_at', 'updated_at'];

    public static $columnas_cond = ['nombre_completo', 'telefono','calle', 'col', 'ciudad', 'created_at', 'updated_at'];

    public static $filtros = [
    	'Activos' => 'situacion,equal,Activo',
    	'Inactivos' => 'situacion,equal,Inactivo',
    	'SinCredito' => 'situacion,equal,Activo',
    	'ConCredito' => 'situacion,equal,Activo',
    ];
    
    public static $tipos =[
        'Activos' => 'Activos',
        'Inactivos' => 'Inactivos',
        'SinCredito' => 'Credito Inactivo',
        'ConCredito' => 'Credito Activo'
    ];

    public function scopeActive($query){
    	return $query->where('clients.situacion', 'Activo');
    }
    public function scopeInactive($query){
        return $query->where('clients.situacion', 'Inactivo');
    }
    public function scopeActiveCredit($query){
        return $query->leftJoin('sales', function($join){
                    $join->on('clients.master_id', '=', 'sales.client_id');
                })
                ->where('sales.situacion', 'Pendiente');
    }
    public function scopeInactiveCredit(){

    }
    protected static function boot(){
        parent::boot();
        static::addGlobalScope(new SearchPaginateAndOrder);
    }
    public function sales(){
    	return $this->hasMany(Sale::class, 'client_id');
    }
}
