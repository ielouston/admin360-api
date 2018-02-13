@extends('layouts.master')

@section('title')
	Mueblerias | Inicia Sesión
@stop
@section('content')
	@include('partials.navs.default')

	<div class="micro-block">
		<form action="{{ route('admin_login_submit') }}" method="POST" accept-charset="utf-8" class="form-horizontal" role="form">
			
			<h1><center>Iniciar sesión</center></h1>
			<hr>
			<div class="form-group {!! $errors->has('name') ? 'has-error' : '' !!}">
				<label for="name" class="col-md-4 control-label">Usuario</label>
				
				<div class="col-md-6">
					<input type="text" name="name" value="{{old('name')}}" placeholder="usuario" class="form-control">
					
					@if($errors->has('name'))
						<span class="help-block">
							<strong></strong>
						</span>
					@endif
				</div>
			</div>

			<div class="form-group {{ $errors->has('password') ? ' has-error' : '' }}">
				<label for="name" class="col-md-4 control-label">Contraseña</label>
				
				<div class="col-md-6">
					<input type="password" name="password" value="{{ old('password') }}" placeholder="contraseña" class="form-control">
				
				@if($errors->has('password'))
					<span class="help-block">
						<strong>{{ $errors->first('password') }}</strong>
					</span>
				@endif
				</div>
			</div>

			{{-- <div class="form-group">
				<div class="col-md-6 col-md-offset-4">
					<input type="checkbox" name="remember" {{ old('remember') ? 'checked' : '' }} class="form-control">
					<label for="">Recordar sesion?</label>
						
					
				</div>
			</div> --}}
			
			<div class="form-group">
				<div class="col-md-6 col-md-offset-4">
					<button type="submit" clas="btn btn-primary btn-block">
						Iniciar sesión
					</button>
				</div>
			</div>
			<input type="hidden" name="_token" value="{{ csrf_token() }}">
		</form>
	</div>
@stop