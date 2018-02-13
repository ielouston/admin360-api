<?php

namespace Muebleria\Http\Controllers;

use Illuminate\Http\Request;
use Muebleria\Business;
use Validator;

class BusinessController extends Controller
{
    public function store(Request $request){
    	$validator = Validator::make($request->all(), [
    		'nombre' => 'string|required',
    		'rfc' => 'string|required',
            'calle' => 'string|required',
            'numero' => 'string|required',
            'colonia' => 'string|required',
            'cod_postal' => 'string|required',
            'ciudad' => 'string|required',
            'estado' => 'string|required',
    		'tipo' => 'string|required',
    		'usuario_id' => 'integer|required',
            'telefonos' => 'string|required'
    	]);

    	if($validator->fails()){
    		return response()->json(0, 400);
		}

		$business = new Business;
		$business->nombre = $request->get('nombre');
		$business->rfc = $request->get('rfc');
        $business->calle = $request->get('calle');
        $business->numero = $request->get('numero');
        $business->colonia = $request->get('colonia');
        $business->cod_postal = $request->get('cod_postal');
        $business->ciudad = $request->get('ciudad');
        $business->estado = $request->get('estado');
        $business->telefonos = $request->get('telefonos');
		$business->tipo = $request->get('tipo');
		$business->usuario_id = $request->get('usuario_id');
        

		if($business->save()){
			return response()->json($business->id, 200);
		}
		return response()->json(0, 500);
    }
    public function update(Request $request, $id){
    	
    	$validator = Validator::make($request->all(), [
    		'nombre' => 'string|required',
    		'rfc' => 'string|required',
            'calle' => 'string|required',
            'numero' => 'string|required',
            'colonia' => 'string|required',
            'cod_postal' => 'string|required',
            'ciudad' => 'string|required',
            'estado' => 'string|required',
    		'tipo' => 'string|required',
    		'usuario_id' => 'integer|required',
            'telefonos' => 'string|required'
    	]);

    	if($validator->fails()){
    		return response()->json(0, 400);
		}

		$business = Business::find($id);
		$business->nombre = $request->get('nombre');
        $business->telefonos = $request->get('telefonos');
		$business->rfc = $request->get('rfc');
        $business->calle = $request->get('calle');
        $business->numero = $request->get('numero');
        $business->colonia = $request->get('colonia');
        $business->cod_postal = $request->get('cod_postal');
        $business->ciudad = $request->get('ciudad');
        $business->estado = $request->get('estado');
		$business->tipo = $request->get('tipo');
		$business->usuario_id = $request->get('usuario_id');
        $business->avatar = $request->get('avatar');
        
		if($business->update()){
			return response()->json($business->id, 200);
		}
		return response()->json(0, 500);
    }
    public function exists($name){
        $business = Business::where('nombre', $name)->get(['id']);

        if($business->isEmpty()){
            return false;
        }
        return true;
    }
    public function get($type){

        if(!is_null(($type))){
            switch ($type) {
                case 'all':
                    $businesses = Business::all();
                    break;
                case 'virtuals': 
                    $businesses = Business::Virtuals()->get();
                    break;
                case 'physicals': 
                    $businesses = Business::Physicals()->get();
                    break;
                default:
                    $businesses = null;
                    return response()->json('Unathorized type', 401);
                    break;
            }
        }
        
        return response()->json($businesses, 200);
    }
    public function getBy($id){
        $business = Business::find($id);

        if(is_null($business)){
            return response()->json(0, 404);
        }
        return response()->json($business, 200);
    }
}
