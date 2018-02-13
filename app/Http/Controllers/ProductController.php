<?php

namespace Muebleria\Http\Controllers;

use Illuminate\Http\Request;


class ProductController extends Controller
{
 	public function mainTable($id_business){
 		return view('products.main', ['business_id' => $id_business]);
 	}   
}
