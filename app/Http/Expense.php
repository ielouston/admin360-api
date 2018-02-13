<?php

namespace Muebleria;

use Illuminate\Database\Eloquent\Model;

class Expense extends Model
{
	protected $id;
    protected $nombre;

    protected $fillable = [
    	'nombre'
    ];

    public function movements(){
    	return $this->hasMany(Movement::class, 'expense_id');
    }
}
