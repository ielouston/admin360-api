<?php

namespace Muebleria\Http\Controllers\Auth;

use Illuminate\Http\Request;
use Muebleria\Http\Controllers\Controller;
use Muebleria\Http\Controllers\AuthenticateController;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Validator;
use Illuminate\Support\Facades\Auth;

class AdminLoginController extends Controller
{

	use AuthenticatesUsers;

	protected $redirecTo = '/admin';

	public function guard(){
		return Auth::guard('admin');
	}

	public function __construct(){
		$this->middleware('guest:admin', ['except' => 'logout']);
	}
    
    public function showLoginForm(){
    	return view('admin.login');
    }

    public function validate_data($data){

    	return Validator::make($data , [
    		'name' => 'string|required',
    		'password' => 'string|required'
    	]);

    }
    public function login(Request $request){
    	$credentials = $request->only(['name', 'password']);
		$validator = $this->validate_data($credentials);    	

    	if($validator->fails()){
    		return response()->json(0, 400);
    	}

    	$remember = $request->get('remember') == 'on' ? true : false;
    	
    	if(Auth::guard('admin')->attempt($credentials, $remember)){
            //Retrieve and send the token to the dashboard
            $cUserA = new AuthenticateController();
            $token = $cUserA->getToken($credentials);
            session(['token' => $token]);
            
    		return redirect()->route('admin_dashboard');
    	}
        
    	return redirect()->back()->withErrors()->withInput();
    }

    public function logout(){
        Auth::guard('admin')->logout();

        return redirect()->route('admin_login');
    }
}
