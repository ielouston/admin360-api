<?php

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;
use Muebleria\Business;

class BusinessesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Business::truncate();

        Business::create([
        	'nombre' => 'Muebleria Frayde',
        	'calle' => 'José Sotero de Castañeda',
            'numero' => '29',
            'colonia' => 'Centro',
            'cod_postal' => '60600',
            'rfc' => 'RAGF671204JX9',
            'ciudad' => 'Apatzingán',
            'estado' => 'Michoacán',
        	'tipo' => 'Matriz',
        	'usuario_id' => 1,
        	'telefonos' => '0'
        ]);

        Business::create([
        	'nombre' => 'Muebleria Castañon',
        	'calle' => 'José Sotero de Castañeda',
            'numero' => '29',
            'colonia' => 'Centro',
            'cod_postal' => '60600',
            'rfc' => 'RAGF671204JX9',
            'ciudad' => 'Apatzingán',
            'estado' => 'Michoacán',
        	'tipo' => 'Sucursal',
        	'usuario_id' => 1,
        	'telefonos' => '0'
        ]);

        Business::create([
        	'nombre' => 'Expo Muebles',
        	'calle' => 'José Sotero de Castañeda',
            'numero' => '29',
            'colonia' => 'Centro',
            'cod_postal' => '60600',
            'rfc' => 'RAGF671204JX9',
            'ciudad' => 'Apatzingán',
            'estado' => 'Michoacán',
        	'tipo' => 'Sucursal',
        	'usuario_id' => 1,
        	'telefonos' => '0'
        ]);

        Business::create([
            'nombre' => 'Muebleria Virtual',
            'calle' => 'Ciberespacio',
            'numero' => '666',
            'colonia' => '999',
            'cod_postal' => '69696',
            'rfc' => 'RAGF69696969',
            'ciudad' => 'Baggins Shrine',
            'estado' => 'Mordor',
            'tipo' => 'Matriz',
            'usuario_id' => 1,
            'telefonos' => '0'
        ]);

    }
}
