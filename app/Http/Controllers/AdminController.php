<?php

namespace Muebleria\Http\Controllers;

use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function __construct(){
    	$this->middleware('auth:admin');
    }
    
    public function index(){
    	return view('home');
    }

    public function home(){
    	return view('welcome');
    }

    public function indexAdmin(){
    	return view('admin.dashboard');
    }
}
