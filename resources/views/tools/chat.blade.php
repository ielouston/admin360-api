@extends('layouts.default')

@section('title')
	Envia un mensaje al personal | Chat
@stop

@section('content')
	<h1>Mensajes de : </h1>
	<chat-message></chat-message>
	<chat-log></chat-log>
	<chat-composer></chat-composer>
@stop