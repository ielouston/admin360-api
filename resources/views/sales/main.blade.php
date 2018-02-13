@extends('layouts.master')

@section('title')
	Ventas de Mueblerias
@stop
@section('nav')
	@include('partials.navs.default')
@stop
@section('content')
		<data-viewer source="/api/ventas" 
					title="Lista de Ventas" 
					type="VCR" 
					col="updated_at" 
					token="{{ session('token') }}" 
					business_id="1">
		</data-viewer>
		
@stop