<?php

namespace Muebleria\Http\Repositories;

use Illuminate\Http\Request;
use Muebleria\Http\Controllers\Controller;
use Muebleria\Client;
use Validator;


class ClientRepository extends Controller
{
    public function getActives($id_bussiness){
        
        $clients = Client::Active()->get();
        
        return $clients;
    }
    public function getInactives($id_bussiness){
        $clients = Client::Inactive()->get();
        return $clients;
    }
    public function getWithCredit($id_bussiness){
        
        $clients = Client::ActiveCredit()->Active()->get(['clients.*']);
        return $clients;
    }

}
