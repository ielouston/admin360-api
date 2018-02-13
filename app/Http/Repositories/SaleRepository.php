<?php

namespace Muebleria\Http\Repositories;

use Illuminate\Http\Request;
use Muebleria\Http\Controllers\Controller;
use Muebleria\Sale;
use Validator;

class SaleRepository extends Controller
{

	public function getUnfinished($id_bussiness){
		$sales = Sale::Unfinished()->get();
        return $sales;
	}
    public function getByType($id_bussiness, $type){

        $sales = Sale::Type($type)->get();
        
        return $sales;
    }
    public function getCanceled($id_bussiness){
        $sales = Sale::where('sales.situacion', 'Cancelada')->get();
        
        return $sales;
    }

}
