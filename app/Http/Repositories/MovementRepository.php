<?php

namespace Muebleria\Http\Repositories;

use Muebleria\Http\Controllers\Controller;
use Muebleria\Movement;

class MovementRepository extends Controller
{
	public function getAll($date_from, $date_to, $both = false, $business_id, $type){
        $query = null;
        switch ($type) {
            case 'generales':
                $query = Movement::where('movimiento', '!=', 'GASTO');
                break;

            case 'ventas':
                $query = Movement::whereIn('movimiento', array('ABNV', 'ACAN', 'DCAN', 'VAP', 'VCR', 'VTR', 'VCO', 'INVS', 'INVSTR', 'INVEC'));
                break;

            case 'compras':
                $query = Movement::whereIn('movimiento', array('ABNV', 'ACAN', 'DCAN', 'CCO', 'INVE', 'INVETR', 'INVSC'));
                break;

            case 'productos':
                $query = Movement::whereIn('movimiento', array('INVE', 'INVETR', 'INVS', 'INVSC', 'INVSTR'));
                break;

            case 'gastos':
                $query = Movement::where('movimiento', 'GASTO');
                break;
            
            default:
                return response()->json(0, 400);
                break;
        }

        if($business_id > 0){
            $query->where('business_id', $business_id);
        }

		if($both == "true"){
            if (strlen($date_from) > 0 && $both){
                $query->where('fecha', '>', $date_from);
                $query->orWhere('fecha', 'like', $date_from .'%');
            }
            if(strlen($date_to) > 0 && $both){
                $query->where('fecha', '<=', $date_to);
            }
        }
        else { 
            $query->where('fecha', 'like', $date_from . '%');   
        }
        $query->orderBy('fecha', 'ASC');
        return $query->get();
	}
    
}
