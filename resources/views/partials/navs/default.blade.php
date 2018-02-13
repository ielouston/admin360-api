<nav role="navigation" class="navbar navbar-default">
	<div class="navbar-header">
        <a href="#" class="navbar-brand">Mueblerias</a>
    </div>
	<div class="collapse navbar-collapse">
		<ul class="nav navbar-nav">
			<li class="active"><a href="{{route('main_clients', 1)}}">Clientes</a></li>
			<li><a href="{{ route('main_products', 1) }}">Productos</a></li>
			<li><a href="{{route('main_sales', 1)}}">Ventas</a></li>
			<li><a href="{{ route('movements_main') }}">Movimientos</a></li>
		</ul>

		<ul class="nav navbar-nav navbar-right">
			@if(Auth::check())
				<li><a href="{{ route('admin_logout') }}">Cerrar Sesión</a></li>
			@else
				<li><a href="{{ route('admin_login') }}">Iniciar Sesión</a></li>
			@endif
		</ul>
	</div>
</nav>