<?php

use Illuminate\Database\Seeder;
use Muebleria\Admin;

class AdminTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Admin::truncate();

        Admin::create([
        	'name' => 'jorge',
        	'password' => bcrypt('joyGames!'),
        	'email' => 'admin@email.com',
        	'device' => 'Generado',
        	'type' => 1,
        	'situacion' => 'Activo'
        ]);

        Admin::create([
        	'name' => 'alan',
        	'password' => bcrypt('sosa'),
        	'email' => 'alan.sosa.g@hotmail.com',
        	'device' => 'Generado',
        	'type' => 1,
        	'situacion' => 'Activo'
        ]);

        Admin::create([
        	'name' => 'lupita',
        	'password' => bcrypt('lupit4!'),
        	'email' => 'manager@email.com',
        	'device' => 'Generado',
        	'type' => 1,
        	'situacion' => 'Activo'
        ]);


    }
}
