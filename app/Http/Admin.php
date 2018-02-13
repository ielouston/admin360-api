<?php

namespace Muebleria;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class Admin extends Authenticatable
{
	use Notifiable;

    protected $guard = 'admin';
    // protected $secret_key = env('ADMIN_KEY');
    protected $fillable = [
    	'name', 'password', 'situacion', 'device'
    ];
    protected $types = ['God', 'Manager', 'Seller', 'Driver'];
    protected $hidden = [
    	'password', 'secret_key'
    ];

    public function businesses(){
    	return $this->hasMany(Business::class, 'business_id');
    }
}
