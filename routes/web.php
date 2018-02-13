<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| This file is where you may define all of the routes that are handled
| by your application. Just tell Laravel the URIs it should respond
| to using a Closure or controller method. Build something great!
|
*/
Route::get('/', [
	'uses' => 'AdminController@index',
	'as' => 'index'
]);

Route::get('/home', [
	'uses' => 'AdminController@home',
	'as' => 'home'
]);

Route::get('/login', [
	'uses' => 'Auth\LoginController@showLoginForm',
	'as' => 'login'
]);

Route::get('/admin/login', [
	'uses' => 'Auth\AdminLoginController@showLoginForm',
	'as' => 'admin_login'
]);

Route::post('/admin/login', [
	'uses' => 'Auth\AdminLoginController@login',
	'as' => 'admin_login_submit'
]);

Route::get('/admin/logout', [
	'uses' => 'Auth\AdminLoginController@logout',
	'as' => 'admin_logout'
]);

Route::group(['prefix' => 'admin', 'middleware' => 
	'auth:admin'], function (){

	Route::get('/', [
		'uses' => 'AdminController@indexAdmin',
		'as' => 'admin_dashboard'
	]);		

	Route::get('clientes/{id_bussiness}', [
		'uses' => 'ClientController@mainTable',
		'as' => 'main_clients'
	]);

	Route::get('ventas/{id_bussiness}', [
		'uses' => 'SaleController@mainTable',
		'as' => 'main_sales'
	]);
	
	Route::get('productos/{id_bussiness}', [
		'uses' => 'ProductController@mainTable',
		'as' => 'main_products'
	]);

	Route::get('movimientos', [
		'uses' => 'MovementController@main',
		'as' => 'movements_main'
	]);

});





