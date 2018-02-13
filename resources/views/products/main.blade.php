@extends('layouts.master')

@section('title')
	Productos de Mueblerias en General
@stop
@section('nav')
	@include('partials.navs.default')
@stop
@section('content')
		<data-viewer source="/api/productos" 
					 title="Lista de Productos" 
					 type="Activos"
					 col="updated_at" 
					 token="{{ session('token') }}"
					 business_id="0">
		</data-viewer>
		<modal show="false"></modal>
@stop

@section('scripts')
	<script src="{{ asset('js/jquery-2.1.0.min.js') }}"></script>
@stop