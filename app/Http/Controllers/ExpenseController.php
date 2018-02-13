<?php

namespace Muebleria\Http\Controllers;
use Muebleria\Expense;
use Illuminate\Http\Request;
use Validator;

class ExpenseController extends Controller
{
    public function store(Request $req){
    	
    	$validator = Validator::make($req->only(['nonbre']), [
    		'nombre' => 'string|required'
    	]);

    	if($validator->fails()){
    		return response()->json(0, 400);
    	}
    	
    	if($this->exist($req->get('nombre')) == 0){
    		$exp = new Expense;
    		$exp->nombre = $req->get('nombre');

    		if($exp->save()){
    			return response()->json($exp->id, 200);
    		}
    	}
    	
    	return response()->json(0, 500);
    }
    public function update(Request $req, $id){
        
        $validator = Validator::make($req->only(['nombre']), [
            'nombre' => 'string|required'
        ]);

        if($validator->fails()){
            return response()->json(0, 400);
        }
        
        if($this->exist($req->get('nombre')) == 0){
            $exp = Expense::find($id);
            $exp->nombre = $req->get('nombre');

            if($exp->save()){
                return response()->json($exp->id, 200);
            }
        }
        
        return response()->json(0, 500);
    }
    public function exist($nombre){
    	$exp = Expense::where('nombre', $nombre)->first();

    	if(is_null($exp)){
    		return 0;
    	}
    	return $exp->id;
    }
    public function getAll(){
    	$exps = Expense::all();

    	return response()->json($exps, 200);
    }
}
