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
        	'name' => 'daysi',
        	'password' => bcrypt('virruet4!'),
        	'email' => 'admin@email.com',
        	'device' => 'Generado',
        	'type' => 1,
        	'situacion' => 'Activo'
        ]);

        Admin::create([
        	'name' => 'ramon',
        	'password' => bcrypt('ramon'),
        	'email' => 'manager@email.com',
        	'device' => 'Generado',
        	'type' => 2,
        	'situacion' => 'Activo'
        ]);


    }
}
