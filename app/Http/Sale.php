<?php

namespace Muebleria;

use Illuminate\Database\Eloquent\Model;
use Muebleria\Scopes\SearchPaginateAndOrder;

class Sale extends Model
{
    protected $master_id;
    protected $nota;
    protected $client_id;
    protected $business_id;
    protected $calle;
    protected $numero;
    protected $colonia;
    protected $cod_postal;
    protected $telefono;
    protected $ciudad;
    protected $tipo_venta;
    protected $fecha;
    protected $hora;
    protected $anticipo;
    protected $descuento;
    protected $plazo;
    protected $vencimiento;
    protected $prorroga;
    protected $subtotal;
    protected $total;
    protected $saldo_actual;
    protected $pagado;
    protected $inversion;
    protected $productos;
    protected $usuario;
    protected $situacion;
    protected $salidas;
    protected $comentarios;
    protected $cliente;
    protected $intereses;
    
    public static $table_name = 'sales';

    protected $fillable = ['nota', 'client_id', 'business_id', 'calle', 'numero', 'col', 'cod_postal', 'telefono', 'ciudad', 'tipo_venta', 'fecha', 'hora', 'anticipo', 'descuento', 'plazo', 'vencimiento', 'prorroga', 'subtotal', 'total', 'saldo_actual', 'pagado', 'inversion', 'productos', 'usuario', 'situacion', 'salidas', 'comentarios', 'cliente', 'intereses'
    ];

    public static $columnas_tabla = ['nota','telefono', 'ciudad', 'subtotal', 'descuento', 'total', 'saldo_actual', 'pagado', 'cliente','updated_at'
    ];

    public static $columnas_cond = ['nota', 'cliente', 'telefono', 'ciudad', 'subtotal', 'descuento', 'total', 'saldo_actual', 'pagado', 'updated_at'
    ];

    public static $tipos =[
        'VCR' => 'Credito',
        'VCO' => 'Contado',
        'VAP' => 'Apartado',
        'VTR' => 'Traspaso',
        'Pend' => 'Pendiente',
        'Canc' => 'Cancelada'
    ];

    public function build(array $build){
    	$this->nota = $build['Nota'];
    	$this->client_id = $build['ClientID'];
    	$this->business_id = $build['business_id'];
    }
    public function clients(){
    	return $this->belongsTo(Client::class, 'client_id');
    }
    public function payments(){
        return $this->hasMany(Payment::class, 'sale_id');
    }
    public function scopeUnfinished($query){
        return $query->where('sales.salidas', '0');
    }
    public function scopeType($query, $type = 'VCR'){
        return $query->where('sales.tipo_venta', $type);
    }
    public function scopeCanceled($query){
        return $query->where('sales.situacion', 'Cancelada');
    }
    protected static function boot(){
        parent::boot();
        static::addGlobalScope(new SearchPaginateAndOrder);
    }
}
