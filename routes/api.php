<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
*/
Route::group(['middleware' => 'cors'], function () {
	/**
	 * Retrieve all businesses
	 */
	Route::get('/negocios/{type}', [
		'uses' => 'BusinessController@get',
		'as'   => 'business_get' 
	])->where('type', '[a-zA-z]+');
	/**
	 * Retrieve products and stocks from business_id
	 * default 0 retrieves all.
	 */
	Route::get('productos/listar/{id_business}', [
		'uses' => 'ProductApiController@get',
		'as'   => 'products_get' 
	]);
	
	Route::get('productos/{category}/{id_business}', [
		'uses' => 'ProductApiController@getByCategory',
		'as' => 'products_by_category'
	])->where('category', '[a-zA-z]+')->where('id_business', '[\d+]+');

	Route::get('productos/categorias', [
		'uses' => 'ProductApiController@getCategories',
		'as' => 'products_categories'
	]);
    /**
     * get searching products
     */
    Route::get('productos/buscar', [
    	'uses' => 'ProductApiController@searchInModel',
    	'as' => 'products_search'
    ]);
    
	Route::get('productos/{id}/{business_id}', [
		'uses' => 'ProductApiController@find',
		'as'   => 'product_find' 
	])->where('id', '[\d+]+');

	Route::get('productos/inicio', [
		'uses' => 'ProductApiController@home',
		'as' => 'products_get_home'
	]);

});

Route::resource('authenticate', 'AuthenticateController', ['only' => ['index']]);
Route::post('authenticate/{business_id}', 'AuthenticateController@authenticate');

Route::post('admin/login', [
	'uses' => 'Auth\AdminApiController@login',
	'as' => 'admin_login'
]);

// admin's routes
Route::group(['middleware' => 'admin_auth'], function (){

	Route::post('archivos/subir/{folder}', [
		'uses' => 'FileController@upload',
		'as' => 'file_upload'
	]);	
});	

// regular users
Route::group(['middleware' => 'jwt.auth'], function (){
	
	Route::post('archivos/subir/{folder}', [
		'uses' => 'FileController@upload',
		'as' => 'file_upload'
	]);

	Route::get('avatar/{perfil}', [
		'uses' => 'FileController@serveAvatar',
		'as' => 'serve_avatar'
	]);

	Route::post('/negocios', [
		'uses' => 'BusinessController@store',
		'as'   => 'business_save' 
	]);	

	Route::patch('/negocios/{id}', [
		'uses' => 'BusinessController@update',
		'as'   => 'business_update' 
	]);

	Route::get('/negocios/{id}', [
		'uses' => 'BusinessController@getBy',
		'as'   => 'business_get' 
	])->where('id', '[\d+]+');

	Route::post('/compras', [
		'uses' => 'BuyApiController@store',
		'as' => 'buy_store'
	]);
	
	Route::post('/compras/importar', [
		'uses' => 'BuyApiController@import',
		'as' => 'buys_import'
	]);

	Route::post('/compras/cancelar', [
		'uses' => 'BuyApiController@cancel',
		'as' => 'buy_cancel'
	]);
	
	Route::get('/compras/{business_id}/todas', [
		'uses' => 'BuyApiController@getAll',
		'as' => 'buys_get_all'
	]);

	Route::get('compras/{id}', [
		'uses' => 'BuyApiController@find',
		'as' => 'buy_find'
	]);
	
	Route::post('clientes', [
		'uses' => 'ClientApiController@save',
		'as'   => 'client_save' 
	]);

	Route::get('clientes/tabla', [
		'uses' => 'ClientApiController@getTable',
		'as'   => 'clients_get_table' 
	]);

	Route::patch('clientes/{id}', [
		'uses' => 'ClientApiController@update',
		'as'   => 'client_update' 
	]);

	Route::get('clientes/{id}', [
		'uses' => 'ClientApiController@find',
		'as'   => 'client_find' 
	]);

	Route::get('clientes/listar/todos', [
		'uses' => 'ClientApiController@getAll',
		'as'   => 'clients_get_all' 
	]);

	Route::post('clientes/importar', [
		'uses' => 'ClientApiController@import',
		'as'   => 'clients_import' 
	]);

	Route::post('movimientos', [
		'uses' => 'MovementApiController@store',
		'as' => 'movement_store'
	]);

	Route::patch('movimientos/{id}', [
		'uses' => 'MovementApiController@update',
		'as' => 'movement_update'
	])->where('id', '[\d+]+');

	Route::get('movimientos/{type}', [
		'uses' => 'MovementApiController@getAll',
		'as' => 'movement_store'
	])->where('type', '[a-zA-z]+');

	Route::post('movimientos/importar', [
		'uses' => 'MovementApiController@import',
		'as' => 'movements_import'
	]);

	Route::get('movimientos/{business_id}/todos', [
		'uses' => 'MovementApiController@getByBusiness',
		'as' => 'movements_get_all'
	]);

	Route::post('movimientos/fecha', [
		'uses' => 'MovementApiController@updateDates',
		'as' => 'movements_date_update'
	]);

	Route::post('productos', [
		'uses' => 'ProductApiController@store',
		'as'   => 'product_save' 
	]);

	Route::get('productos/tabla', [
		'uses' => 'ProductApiController@getTable',
		'as'   => 'products_get_table' 
	]);

	

	Route::get('productos/clave/{clave}', [
		'uses' => 'ProductApiController@getBy',
		'as'   => 'product_get_key' 
	]);

	Route::get('productos/exist/{prod_key}', [
		'uses' => 'ProductApiController@exists',
		'as' => 'products_check'
	]);

	Route::patch('productos/{id}', [
		'uses' => 'ProductApiController@update',
		'as'   => 'product_update' 
	]);

	Route::post('productos/importar', [
		'uses' => 'ProductApiController@import',
		'as'   => 'products_import' 
	]);

	Route::post('/proveedores', [
		'uses' => 'ProviderApiController@store',
		'as' => 'provider_store'
	]);

	Route::patch('/proveedores/{id}', [
		'uses' => 'ProviderApiController@update',
		'as' => 'provider_update'
	]);

	Route::get('/proveedores', [
		'uses' => 'ProviderApiController@get',
		'as' => 'provider_get'
	]);

	Route::post('/proveedores/importar', [
		'uses' => 'ProviderApiController@import',
		'as' => 'providers_import'
	]);

	Route::patch('/stocks/{id}', [
		'uses' => 'StockController@update',
		'as'   => 'stock_update' 
	]);

	Route::post('/queues', [
		'uses' => 'QueueController@store',
		'as'   => 'queue_save' 
	]);

	Route::patch('/queues/{id}', [
		'uses' => 'QueueController@update',
		'as'   => 'queue_update' 
	]);

	Route::get('/queues/{id_business}/{device}/{type}/{queueID}', [
		'uses' => 'QueueController@getByBusiness',
		'as'   => 'queue_get' 
	]);

	Route::get('/queues/{id_business}', [
		'uses' => 'QueueController@lastQueueID',
		'as'   => 'queue_get_last' 
	]);

	Route::post('queues/importar', [
		'uses' => 'QueueController@import',
		'as'   => 'queues_import'
	]);
	
	Route::post('/ventas', [
		'uses' => 'SaleApiController@save',
		'as' => 'sales_store'
	]);

	Route::post('/ventas/movs', [
		'uses' => 'SaleApiController@regenerateMovements',
		'as' => 'sale_regenerate_movs'
	]);

	Route::get('/ventas/tabla', [
		'uses' => 'SaleApiController@getTable',
		'as'   => 'sales_get_table' 
	]);
	
	Route::get('ventas/cliente/{id}/{business_id}', [
		'uses' => 'SaleApiController@getByClient',
		'as' => 'sales_by_client'
	]);
	
	Route::get('/ventas/{id}', [
		'uses' => 'SaleApiController@find',
		'as' => 'sale_find'
	]);	

	Route::patch('/ventas/{id}', [
		'uses' => 'SaleApiController@update',
		'as' => 'sales_update'
	]);	

	Route::get('/ventas/{id_bussiness}/todas', [
		'uses' => 'SaleApiController@getAll',
		'as'   => 'sales_get_all' 
	]);

	Route::post('/ventas/importar', [
		'uses' => 'SaleApiController@import',
		'as' => 'sales_import'
	]);	

	Route::post('ventas/prorrogar', [
		'uses' => 'SaleApiController@extend',
		'as' => 'sale_extend'
	]);

	Route::post('ventas/cancelar', [
		'uses' => 'SaleApiController@cancel',
		'as' => 'sale_cancel'
	]);

	Route::post('/pagos', [
		'uses' => 'PaymentController@store',
		'as' => 'pay_store'
	]);

	Route::post('/pagos/cancelar', [
		'uses' => 'PaymentController@cancel',
		'as' => 'pay_cancel'
	]);

	Route::patch('/pagos/{id}', [
		'uses' => 'PaymentController@update',
		'as' => 'pay_update'
	]);

	Route::get('pagos/{business_id}/todos', [
		'uses' => 'PaymentController@getAll',
		'as' => 'payments_get_all'
	]);

	Route::post('/gastos', [
		'uses' => 'ExpenseController@store',
		'as' => 'expense_store'
	]);

	Route::patch('/gastos/{id}', [
		'uses' => 'ExpenseController@update',
		'as' => 'expense_update'
	]);

	Route::get('/gastos/todos', [
		'uses' => 'ExpenseController@getAll',
		'as' => 'expenses_get_all'
	]);

	Route::post('/inventarios', [
		'uses' => 'InventoryController@store',
		'as' => 'inventory_store'
	]);

	Route::post('/inventarios/importar', [
		'uses' => 'InventoryController@import',
		'as' => 'inventories_import'
	]);

	Route::get('inventarios/{business_id}/todos', [
		'uses' => 'InventoryController@getAll',
		'as' => 'inventories_get_all'
	]);
	
	Route::post('usuarios', [
		'uses' => 'UserController@store',
		'as'   => 'user_store' 
	]);

	Route::patch('/usuarios/{id}', [
		'uses' => 'UserController@update',
		'as'   => 'user_update' 
	]);

	Route::post('/usuarios/importar', [
		'uses' => 'UserController@import',
		'as' => 'users_import'
	]);

	Route::get('/usuarios/{business_id}/todos', [
		'uses' => 'UserController@getAll',
		'as' => 'users_import'
	]);

	Route::get('/usuarios/token', [
		'uses' => 'UserController@byToken',
		'as' => 'user_by_token'
	]);

	Route::get('/movimientos/productos/refactorizar', [
		'uses' => 'MovementApiController@productsRefactor',
		'as' => 'movements_products_refactor'
	]);
});






