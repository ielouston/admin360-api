<?php

namespace Muebleria\Http\Controllers;

use Illuminate\Http\Request;

class MovementController extends Controller
{
    public function main(){
        return view('movements.main');
    }
    
}
