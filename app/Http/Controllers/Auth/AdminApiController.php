<?php

namespace Muebleria\Http\Controllers\Auth;

use Illuminate\Http\Request;
use Muebleria\Http\Controllers\Controller;
use Muebleria\Http\Controllers\AuthenticateController;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Validator;
use Illuminate\Support\Facades\Auth;

class AdminApiController extends Controller
{

	use AuthenticatesUsers;

	protected $redirecTo = '/admin';

	public function guard(){
		return Auth::guard('admin');
	}

	public function __construct(){
		$this->middleware('guest:admin', ['except' => 'logout']);
	}

    public function validate_data($data){

        return Validator::make($data , [
            'name' => 'string|required|max:18',
            'password' => 'string|required|max:18'
        ]);
    }

    public function login(Request $request){
    	$credentials = $request->only(['name', 'password']);
		$validator = $this->validate_data($credentials);    	

    	if($validator->fails()){
    		return response()->json(0, 400);
    	}
    	
    	if(Auth::guard('admin')->attempt($credentials, false)){
            //Retrieve and send the token to the dashboard
            $cUserA = new AuthenticateController();
            $token = $cUserA->getToken($credentials);
            
    		return response()->json($token, 200);
    	}
    	return response()->json(0, 500);
    }
}
