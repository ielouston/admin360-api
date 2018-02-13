@extends('layouts.master')

@section('title')
	Mueblerias Frayde | Venta de Mayoreo y Menudeo
@stop
@section('content')
	@include('partials.navs.default')
	<?php	
		$user = Auth::user();
	?>
	<div class="container full">
		<h1>Bienvenido al sistema web,  {{ Auth::user()->name }}!</h1>	
		
		<pre>Token : <p>{{ Session::get('token') }}</p></pre>
	</div>
	
@stop