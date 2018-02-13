<?php

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;
use Muebleria\User;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        User::truncate();

        User::create([
        	'name' => 'alan',
        	'password' => bcrypt('sosa'),
        	'nombres' => 'Alan Mauricio',
        	'apellidos' => 'Sosa Garcia',
        	'email' => 'alan.sosa.g@hotmail.com',
        	'type' => 1,
        	'device' => 'Generated',
        ]);

        User::create([
        	'name' => 'paco',
        	'password' => bcrypt('rangel'),
        	'nombres' => 'Francisco',
        	'apellidos' => 'Rangel',
        	'email' => 'expomueblesrangel@hotmail.com',
        	'type' => 2,
        	'device' => 'Generated',
        ]);

        User::create([
            'name' => 'agustin',
            'password' => bcrypt('agustinrocio'),
            'nombres' => 'Agusting',
            'apellidos' => 'Hernandez',
            'email' => 'centromueblerocastañon@hotmail.com',
            'type' => 2,
            'device' => 'Generated',
        ]);

        User::create([
            'name' => 'hugo',
            'password' => bcrypt('hugo'),
            'nombres' => 'Hugo',
            'apellidos' => 'Rangel',
            'email' => 'expomueblesrangel@hotmail.com',
            'type' => 2,
            'device' => 'Generated',
        ]);

        User::create([
        	'name' => 'vendedor',
        	'password' => bcrypt('testing'),
        	'nombres' => 'Nombre del vendedor',
        	'apellidos' => 'Apellidos',
        	'email' => 'sin email',
        	'type' => 3,
        	'device' => 'Generated'
        ]);
    }
}
