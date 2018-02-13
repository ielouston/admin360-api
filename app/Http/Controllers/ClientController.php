<?php

namespace Muebleria\Http\Controllers;

use Illuminate\Http\Request;
use Validator;
use Muebleria\Http\Repositories\ClientRepository;

class ClientController extends Controller
{
    public function mainTable($id_bussiness){
        $repo = new ClientRepository();
        return view('clients.main');
    }
}
