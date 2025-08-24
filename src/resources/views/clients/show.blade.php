@extends('layouts.app')

@section('content')
<h1>Cliente #{{ $client->id }}</h1>
<ul>
    <li><b>Nome:</b> {{ $client->name }}</li>
    <li><b>Email:</b> {{ $client->email }}</li>
    <li><b>Telefone:</b> {{ $client->phone }}</li>
    <li><b>Ativo:</b> {{ $client->is_active ? 'Sim' : 'Não' }}</li>
</ul>
<a href="{{ route('clients.edit',$client) }}">Editar</a>
@endsection