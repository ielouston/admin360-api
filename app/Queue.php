<?php

namespace Muebleria;

use Illuminate\Database\Eloquent\Model;

class Queue extends Model
{
	protected $id;
    protected $devices;
    protected $business_id;
    protected $model;
    protected $action;
    protected $data;
    protected $status;

    protected $fillable = [
    	'devices', 'business_id', 'model', 'action', 'data', 'status'
    ];

    protected $guarded = [
    	'data', 'devices'
    ];
}
