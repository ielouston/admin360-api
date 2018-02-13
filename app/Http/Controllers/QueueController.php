<?php

namespace Muebleria\Http\Controllers;

use Illuminate\Http\Request;
use Validator;
use Illuminate\Support\Facades\DB;
use Muebleria\Queue;

class QueueController extends Controller
{
	public function getByBusiness($id_business, $device, $type, $lastQueueID){
        
		$condition = $type == "Sincronizado" ? 'like' : 'not like';
        $ids = array(0, $id_business);
		$queues = Queue::whereIn('business_id', $ids)
				  ->where('devices', $condition, '%'. $device .'%')
                  ->where('id', '>', $lastQueueID)
				  ->get(['created_at', 'action', 'model', 'status', 'data', 'business_id', 'id', 'devices']);
		return response()->json($queues, 200);
	}
    public function store(Request $request){
    	
    	$validator = Validator::make($request->all(), [
    		'devices' => 'string|required',
    		'business_id' => 'integer|required',
    		'model' => 'string|required',
    		'action' => 'string|required',
    		'data' => 'string|required'
		]);
    	
    	if($validator->fails()){
    		return response()->json(0, 400);
    	}

		$q = new Queue;
		$q->devices = $request->get('devices');
		$q->business_id = $request->get('business_id');
		$q->model = $request->get('model');
		$q->action = $request->get('action');
		$q->data = $request->get('data');

		if($q->save()){
			return response()->json($q->id, 200);			
		}
		return response()->json(0, 500);
    }
    /**
     * @param Queue model
     * generate the model sended from the client
     */
    private function generateModel(Queue $queue){

    	switch ($queue->model) {
    		case 'product':
    			switch ($q->action) {
    				case 'create':
    					$cProduct->store($q->data);
    					break;
    				case 'update':
    					$cProduct->update($q->data, $q->master_id);
    					break;
    				default:
    					# code...
    					break;
    			}
    			break;
    		
    		default:
    			# code...
    			break;
    	}
    	return $queue->data;
    }
    public function update(Request $request, $id){
    	
    	$validator = Validator::make($request->all(), [
    		'devices' => 'string|required',
    		'business_id' => 'integer|required',
    		'model' => 'string|required',
    		'action' => 'string|required',
    		'data' => 'string|required'
		]);
    	
		$q = Queue::find($id);
		$q->devices = $q->devices . ',' . $request->get('devices');
		$q->business_id = $request->get('business_id');
		$q->model = $request->get('model');
		$q->action = $request->get('action');
		$q->data = $request->get('data');
		$q->status = "Descargado";

		if($q->save()){
			return response()->json($q->id, 200);
		}
		return response()->json(0, 500);
    }
    public function import(Request $req){
        $queues = json_decode($req->get('queues'), true);
        $response = array();

        foreach ($queues as $queue) {
            $client_id = $queue['ID'];
            $model = Queue::create($queue);
            $response[$client_id] = $model->id;
        }

        return response()->json($response, 200);
    }
    public function lastQueueID($business_id){
    	
    	// $queue = Queue::where('business_id', $business_id)->orderBy('id', 'desc')->first(); 
        $queue = Queue::find(DB::table('queues')->max('id'));
        
    	if(is_null($queue)){
    		return response()->json(0, 404);
    	}
    	return response()->json($queue->id, 200);
    }
}
