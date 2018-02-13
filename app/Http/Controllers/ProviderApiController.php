<?php

namespace Muebleria\Http\Controllers;

use Illuminate\Http\Request;
use Validator;
use Muebleria\Provider;
use Muebleria\Buy;

class ProviderApiController extends Controller
{
    public function __construct(){
        $this->middleware('jwt.auth');
    }
    
    public function store(Request $request){
        
        $validator = Validator::make($request->all(), [
        	'clave' => 'string|required',
        	'nombre' => 'string|required',
        	'telefono' => 'string|required'
        ]);

        if($validator->fails() || $this->exists($request->get('clave')) > 0){
        	return response()->json(0, 400);
        }

        $provider = new Provider;
        $provider->clave = $request->get('clave');
        $provider->nombre = $request->get('nombre');
        $provider->calle = $request->get('calle');
        $provider->numero = $request->get('numero');
        $provider->colonia = $request->get('colonia');
        $provider->cod_postal = $request->get('cod_postal');
        $provider->telefono = $request->get('telefono');
        $provider->telefono2 = $request->get('telefono2');
        $provider->ciudad = $request->get('ciudad');
        $provider->rfc = $request->get('rfc');
        $provider->comentarios = $request->get('comentarios');
        $provider->email = $request->get('email');
        if($provider->save()){
        	return response()->json($provider->id, 200);
        }
        return response()->json(0, 500);
    }
    public function update(Request $request, $id){

    	$validator = Validator::make($request->all(), [
        	'clave' => 'string|required',
        	'nombre' => 'string|required',
        	'telefono' => 'string|required',
        	'ciudad' => 'string|required',
        	'situacion' => 'string|required'
        ]);

        $provider = Provider::find($id);

        if($validator->fails() || is_null($provider)){
        	return response()->json(0, 400);
        }

        
        $provider->clave = $request->get('clave');
        $provider->nombre = $request->get('nombre');
        $provider->calle = $request->get('calle');
        $provider->numero = $request->get('numero');
        $provider->colonia = $request->get('colonia');
        $provider->cod_postal = $request->get('cod_postal');
        $provider->telefono = $request->get('telefono');
        $provider->telefono2 = $request->get('telefono2');
        $provider->ciudad = $request->get('ciudad');
        $provider->rfc = $request->get('rfc');
        $provider->situacion = $request->get('situacion');
        $provider->comentarios = $request->get('comentarios');
        $provider->email = $request->get('email');

        if($provider->update()){
        	return response()->json($provider->id, 200);
        }
        return response()->json(0, 500);
    }
    public function exists($clave){
    	$provider = Provider::where('clave', $clave)->first();

    	if(is_null($provider)){
    		return 0;
    	}
    	return $provider->id;
    }
    public function get(){
    	$providers = Provider::all();

    	if(is_null($providers)){
    		return response()->json(0, 404);	
    	}
    	return response()->json($providers, 200);
    }
    public function import(Request $request){
        $providers = json_decode($request->get('proveedores'), true);
        $model = new Provider;
        $response = array();

        foreach ($providers as $provider) {
            $client_id = $provider['ID'];
            
            $validator = Validator::make($provider, [
                'clave' => 'string|required',
                'nombre' => 'string|required',
                'telefono' => 'string|required',
                'situacion' => 'string|required'
            ]);

            if($validator->fails()){
                return response()->json(0, 400);
            }
            //first get the provider id if is registered
            $id = $this->exists($provider['clave']);
            if($id == 0) {
                $model = Provider::create($provider);
                $ids = $model->id;    
            }
            else {
                $model->id = $id;
                $ids = $id;
            }
            if(count($provider['buys'] > 0)){
                foreach ($provider['buys'] as $buy) {
                    $buy['provider_id'] = $model->id;
                    $buy_model = Buy::create($buy);
                    $ids .= "|".$buy['ID'].",".$buy_model->id;
                }    
            }
            $response[$client_id] = $model->id == 0 ? 0 : $ids;
        }
        return response()->json($response, 200);
    }
}
