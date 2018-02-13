<?php

namespace Muebleria\Http\Controllers;

use Illuminate\Http\Request;
use Validator;
use Muebleria\User;
use JWTAuth;

class UserController extends Controller
{
    public function store(Request $request){
    	
    	$validator = Validator::make($request->all(), [
    		'name' => 'string|required|max:20',
    		'password' => 'string|required|max:20',
    		'type' => 'integer|required'
    	]);

    	if($validator->fails()){
    		return response()->json(0, 400);
    	}
        $id = $this->exists($request->get('name'));
        if($id > 0){
            return response()->json($id, 409);
        }

        $credentials = $request->only(['name', 'password']);
        $user = User::firstOrCreate($credentials, $request->only([
                'name', 'password', 'email', 'type', 'device', 'business_id'
        ]));

    	if($user->save()){
    		return response()->json($user->id, 200);
    	}
    	return response()->json(0, 500);
    }
    private function exists($name){
        $user = User::where('name', $name)->first();

        if(is_null($user)){
            return 0;
        }
        return $user->id;
    }
    public function update(Request $request, $id){
    	
    	$validator = Validator::make($request->all(), [
    		'name' => 'string|required|max:20',
    		'password' => 'string|required|max:20',
    		'type' => 'integer|required'
    	]);

    	if($validator->fails()){
    		return response()->json(0, 400);
    	}

    	$user = User::find($id);
    	$user->name = $request->get('name');
    	$user->password = bcrypt($request->get('password'));
    	$user->email = $request->get('email');
    	$user->type = $request->get('type');
    	$user->device = $request->get('device');
    	$user->nombres = $request->get('nombres');
    	$user->apellidos = $request->get('apellidos');
    	$user->calle = $request->get('calle');
    	$user->numero = $request->get('numero');
    	$user->col = $request->get('col');
    	$user->cod_postal = $request->get('cod_postal');
    	$user->business_id = $request->get('business_id');

    	if($user->update()){
    		return response()->json($user->id, 200);
    	}
    	return response()->json(0, 500);
    }
    public function get(){
    	if(! $user = JWTAuth::parseToken()->authenticate() ){
            return false;
        }

        $token = JWTAuth::getToken();
        $user = JWTAuth::toUser($token);

        return $user;
    }

    public function import(Request $req){
    	$response = array();
    	$model = null;
    	$validator = Validator::make($req->all(), [
    		'usuarios' => 'required'
    	]);

    	if($validator->fails()){
    		return response()->json(0, 400);
    	}

    	$users = json_decode($req->get('usuarios'), true);

		foreach ($users as $user) {
			$client_id = $user['ID'];
			$model = new User;
			$model->name = $request->get('name');
			$model->password = bcrypt($request->get('password'));
			$model->email = $request->get('email');
			$model->type = $request->get('type');
			$model->device = $request->get('device');
			$model->business_id = $request->get('business_id');
			$model->save();
			
			$response[$client_id] = $model->id;
		}

		return response()->json($response, 200);
    }

    public function getAll($business_id){
    	$users = User::where('business_id', $business_id)
    				  ->where('type', '>', 2)
    				  ->get();

    	return response()->json($users, 200);
    }

    public function byToken(){
        
        if (! $user = JWTAuth::parseToken()->authenticate()) {
            return response()->json(0, 404);
        }

        return response()->json($user , 200);
    }
}
