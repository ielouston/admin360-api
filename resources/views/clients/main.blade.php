@extends('layouts.master')

@section('title')
	Clientes de Mueblerias en General
@stop
@section('nav')
	@include('partials.navs.default')
@stop
@section('content')
		<data-viewer source="/api/clientes" 
					 title="Lista de Clientes" 
					 col="updated_at" 
					 type="Activos" 
					 token="{{ session('token') }}"
					 business_id="1">
		</data-viewer>
		<modal show="false"></modal>
@stop

@section('scripts')
	<script src="{{ asset('js/jquery-2.1.0.min.js') }}"></script>
@stop