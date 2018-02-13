<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	
	<title>@yield('title')</title>
	<link rel="stylesheet" href="{{elixir('css/app.css')}}">
	<link href='https://fonts.googleapis.com/css?family=Roboto:300,400,500,700|Material+Icons' rel="stylesheet">
	<link rel="shortcut icon" href="{{ asset('images/favicon.ico') }}">
	@yield('styles')
</head>
<body>
	@yield('nav')
	<div id="app">
		@yield('content')
	</div>
	<script src="/js/app.js"></script>
	@yield('scripts')
</body>

</html>