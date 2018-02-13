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
        	'name' => 'jorge',
        	'password' => bcrypt('joyGames!'),
        	'nombres' => 'Alan Mauricio',
        	'apellidos' => 'Sosa Garcia',
        	'email' => 'alan.sosa.g@hotmail.com',
        	'type' => 1,
        	'device' => 'Generated',
        ]);

        User::create([
        	'name' => 'alan',
        	'password' => bcrypt('sosa'),
        	'nombres' => 'Francisco',
        	'apellidos' => 'Rangel',
        	'email' => 'expomueblesrangel@hotmail.com',
        	'type' => 2,
        	'device' => 'Generated',
        ]);

        User::create([
            'name' => 'lupita',
            'password' => bcrypt('lupit4!'),
            'nombres' => 'Agusting',
            'apellidos' => 'Hernandez',
            'email' => 'centromueblerocastañon@hotmail.com',
            'type' => 2,
            'device' => 'Generated',
        ]);
    }
}
