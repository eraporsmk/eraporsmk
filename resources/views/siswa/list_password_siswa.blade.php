@extends('adminlte::page')

@section('title_postfix', 'Data Password Siswa |')

@section('content_header')
    <h1>Data Password Siswa</h1>
@stop

<?php
/*
@section('box-title')
	Judul
@stop
*/
?>
@section('content')
	<table id="datatable" class="table table-bordered table-striped table-hover">
		<thead>
			<tr>
				<th style="width: 40%" class="text-center">Nama</th>
                <th style="width: 3%" class="text-center">L/P</th>
                <th style="width: 10%" class="text-center">NISN</th>
				<th style="width: 10%" class="text-center">Email</th>
				<th style="width: 10%" class="text-center">Password</th>
			</tr>
		</thead>
		<tbody>
			@foreach ($all_siswa as $pengguna)
			<tr>
				<td>{{strtoupper($pengguna->siswa->nama)}}</td>
				<td>{{$pengguna->siswa->jenis_kelamin}}</td>
				<td>{{$pengguna->siswa->nisn}}</td>
				<td>{{$pengguna->email}}</td>
				<td>
					@if(Hash::check(12345678, $pengguna->password) || Hash::check($pengguna->default_password, $pengguna->password))
					{{$pengguna->default_password}}
					@else 
					{!! '<span class="btn btn-xs btn-success btn-block"> Custom </span>' !!}
					@endif
				</td>
			</tr>
			@endforeach
		</tbody>
	</table>
@endsection

@section('js')
@endsection