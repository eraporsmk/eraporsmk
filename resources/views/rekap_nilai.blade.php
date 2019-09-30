@extends('adminlte::page')

@section('content_header')
    <h1>Dashboard</h1>
@stop

@section('content')
@foreach($all_rombel as $rombel)
	{{$rombel->nama}}=>{{$rombel->nilai_count}}<br />
@endforeach
@stop