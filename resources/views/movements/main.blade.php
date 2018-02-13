@extends('layouts.master')

@section('title')
	Movimientos de Mueblerias en General
@stop
@section('nav')
	@include('partials.navs.default')
@stop
@section('content')
	<movements source="/api/movimientos"
				token="{{ session('token') }}">
	</movements>
@stop

@section('scripts')
	<script src="{{ asset('js/jquery-2.1.0.min.js') }}"></script>
	<script src="https://cdn.jsdelivr.net/lodash/4.17.4/lodash.min.js"></script>
@stop