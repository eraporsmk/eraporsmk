@extends('adminlte::page')

@section('title_postfix', 'Referensi Acuan Sikap |')

@section('content_header')
    <h1>Referensi Acuan Sikap</h1>
@stop

@section('content')
	<table class="table table-bordered">
		<thead>
			@foreach($all_sikap as $sikap)
			<th width="20%" class="text-center">{{$sikap->butir_sikap}}</th>
			@endforeach
		</thead>
		<tbody>
		<tr>
		@foreach($all_sikap as $sikap)
			<td>
			<ul style="padding-left:10px;">
			@foreach($sikap->sikap as $subsikap)
			<li>{{$subsikap->butir_sikap}}</li>
			@endforeach
			</ul>
			</td>
		@endforeach
		</tr>
		</tbody>
	</table>
@stop
