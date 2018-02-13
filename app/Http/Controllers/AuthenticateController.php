<?php

namespace Muebleria\Http\Controllers;

use Illuminate\Http\Request;
use JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;

class AuthenticateController extends Controller
{
	public function __construct()
    {
    	$this->middleware('jwt.auth', ['except' => ['authenticate']]);
	}
    public function authenticate(Request $request, $business_id){
    	$credentials = $request->only('name', 'password');
    	$user_data = $request->only('device', 'business_id');
    	
        $token = $this->getToken($credentials);

        //Update user's rememberToken for future use
        $user = JWTAuth::toUser($token);
        
        $user->device = $user_data["device"];
        $user->business_id = $business_id;

        if($user->type > 2){
        	$user->nombres = $request->get('nombres');
            $user->apellidos = $request->get('apellidos');
        }

        if($user->save()){
            $user->remember_token = $token;
        	return response()->json($token , 200);		
        }        
        return response()->json(0, 500);		
    }
    public function getToken($credentials){
        try {
            if (! $token = JWTAuth::attempt($credentials)) {
                
                return response()->json('bad request', 400);
            }
        } catch (JWTException $e) {
            return response()->json('error', 500);
        }
        return $token;
    }
    public function getUser()
    {
    	if (! $user = JWTAuth::parseToken()->authenticate()) {
	        return response()->json(0, 404);
        }

       	return response()->json(compact('user'));
    }
    public function refresh(){
        if (! $user = JWTAuth::parseToken()->authenticate()) {
            
        }        
    }
}
