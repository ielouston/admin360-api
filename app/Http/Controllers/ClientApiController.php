<?php

namespace Muebleria\Http\Controllers;

use Illuminate\Http\Request;
use Muebleria\Http\Repositories\ClientRepository;
use Muebleria\Client;
use Validator;
use Muebleria\Http\Controllers\FileController;

class ClientApiController extends Controller
{
	public function __construct()
	{
		$this->middleware('jwt.auth');
	}
    
    public function getTable(Request $request){
        $repo = new ClientRepository();
        $clients = null;
        $cols_table = Client::$columnas_tabla;
        $cols_cond = Client::$columnas_cond;
        $filters = Client::$filtros;
        $type = $request->get('type');
        $types = Client::$tipos;
        $id_bussiness = $request->get('business_id');

        switch ($type) {
            case 'Activos':
                $clients = $repo->getActives($id_bussiness);
                break;
            case 'Inactivos':
                $clients = $repo->getInactives($id_bussiness);
                break;
            case 'ConCredito':
                $clients = $repo->getWithCredit($id_bussiness);
                break;
            case 'SinCredito':
                $clients = $repo->getWithoutCredit($id_bussiness);
                break;
            default:
                # code...
                break;
        }
        return response()->json([
            'status' => 'ok', 
            'data' => $clients, 
            'columnas_tabla' => $cols_table,
            'columnas_cond' => $cols_cond,
            'tipos' => $types
        ], 200);
    }
    /**
     * Find client by ID 
     * @param int ID
     * @return Client $client
     */
    public function find($id){
        $client = Client::find($id);

        if(is_null($client)){
            return response()->json(0, 404);
        }
        return response()->json($client, 200);
    }
    public function getAll(){
    	$clients = Client::all();

    	return response()->json($clients, 200);
    }
    public function exist($nombre){
        $client = Client::where('nombre_completo', $nombre)->first();

        if(is_null($client)){
            return 0;
        }
        return $client->id;
    }
    /** 
     * Store a client in DB
     * @param Request POST
     * @return int $id
     */
    public function save(Request $request){
        
        $validator = Validator::make($request->all(),[
            'nombre_completo' => 'string|required',
            'telefono' => 'string|required',
            'usuario' => 'string|required'
        ]);

        if($validator->fails())
        {
            return response()->json(0, 400);
        }
        $id = $this->exist($request->get('nombre_completo'));
        if($id > 0){
            return response()->json($id, 409);
        }

        $avatar_path = '';
    	$client = new Client;
        $client->nombre_completo = $request->get("nombre_completo");
        $client->telefono = $request->get("telefono");
        $client->telefono2 = $request->get("telefono2");
        $client->calle = $request->get("calle");
        $client->numero = $request->get("numero");
        $client->email = $request->get("email");
        $client->rfc = $request->get("rfc");
        $client->edad = $request->get("edad");
        $client->ciudad = $request->get("ciudad");
        $client->calle_e = $request->get("calle_e");
        $client->calle_y = $request->get("calle_y");
        $client->col = $request->get("colonia");
        $client->cod_postal = $request->get("cod_postal");
        $client->cercanias = $request->get("cercanias");
        $client->referencias = $request->get("referencias");
        $client->comentarios = $request->get("comentarios");
        $client->folder = $request->get("folder");
        $client->usuario = $request->get("usuario");
        $client->situacion = "Activo";
        $client->avatar = $request->get('avatar');
        $client->thumb = $request->get('thumb');
        $client->documentos = $request->get('documentos');
        
        //Extract the files
        // $avatar_file = $request->file('avatar');    
        // $cFile = new FileController;
        // $cont = 0;
        

        // if(strlen($avatar_file) > 0){
        //     $file_response = $cFile->upload($avatar_file, $client->nombre_completo);
        // }
        
        //Upload avatar and docs
        // $client->avatar = strlen($avatar_file) > 0 ? $cFile->upload($avatar_file, $client->nombre_completo) : '';
        
        // $client->avatar = $this->uploadFile($request->file("avatar_file"), $client->folder);

        if($client->save()){
            return response()->json($client->id, 200);
        }
        return response()->json(0, 500);
    }
    
    /**
     * @param int $id
     * @param Request PATCH
     * @return int $id
     */
    public function update($id, Request $request){
        $client = Client::find($id);
        
        $validator = Validator::make($request->all(),[
            'nombre_completo' => 'string|required',
            'telefono' => 'string|required',
            'usuario' => 'string|required',
            'situacion' => 'string|required'
        ]);
        $nombre_aux = $request->get('nombre_completo');
        $id = $this->exist($nombre_aux);
        
        if($validator->fails()) 
        {
            return response()->json(0, 400);
        }
        else if($client->nombre_completo != $nombre_aux && $id > 0){
            return response()->json($id, 409);
        }

        $client->nombre_completo = $nombre_aux;
        $client->telefono = $request->get("telefono");
        $client->telefono2 = $request->get("telefono2");
        $client->calle = $request->get("calle");
        $client->numero = $request->get("numero");
        $client->email = $request->get("email");
        $client->rfc = $request->get("rfc");
        $client->edad = $request->get("edad");
        $client->ciudad = $request->get("ciudad");
        $client->calle_e = $request->get("calle_e");
        $client->calle_y = $request->get("calle_y");
        $client->col = $request->get("colonia");
        $client->cod_postal = $request->get("cod_postal");
        $client->cercanias = $request->get("cercanias");
        $client->referencias = $request->get("referencias");
        $client->documentos = $request->get("documentos");
        $client->comentarios = $request->get("comentarios");
        $client->folder = $request->get("folder");
        $client->usuario = $request->get("usuario");
        $client->situacion = $request->get("situacion");
        $client->avatar = $request->get("avatar");
        $client->thumb = $request->get("avatar");
        $client->documentos = $request->get("documentos");

        if($client->update()){
            return response()->json($client->id, 200);
        }
        return response()->json(0, 500);
    }
    /**
     * @param Request
     * @return Array of Model's IDS : client => server 
     */
    public function import(Request $request){
        $clients = json_decode($request->get('clientes'), true);
        $model = new Client;

        foreach ($clients as $client) {
            $client_id = $client['ID'];
            
            $validator = Validator::make($client, [
                'nombre_completo' => 'string|required',
                'telefono' => 'string|required',
                'usuario' => 'string|required',
                'situacion' => 'string|required'
            ]);

            if($validator->fails() || $this->exist($request->get('nombre_completo'))){
                
                $model->id = 0;
            }
            else{
                $model = Client::firstOrCreate(
                    [
                    'nombre_completo' => $client['nombre_completo'] 
                    ],
                    $client
                );    
            }
            
            $response[$client_id] = $model->id == 0 ? 0 : $model->id;
        }
        return response()->json($response, 200);
    }
}
