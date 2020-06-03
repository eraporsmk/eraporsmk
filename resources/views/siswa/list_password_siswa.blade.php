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
				<th style="width: 10%" class="text-center">Atur Ulang Sandi</th>
			</tr>
		</thead>
		<tbody>
			@foreach ($all_siswa as $pengguna)
			<tr>
				<td>{{strtoupper($pengguna->siswa->nama)}}</td>
				<td class="text-center">{{$pengguna->siswa->jenis_kelamin}}</td>
				<td class="text-center">{{$pengguna->siswa->nisn}}</td>
				<td>{{$pengguna->email}}</td>
				<td class="text-center">
					@if(Hash::check(12345678, $pengguna->password) || Hash::check($pengguna->default_password, $pengguna->password))
					{{$pengguna->default_password}}
					@else 
					{!! '<span class="btn btn-xs btn-success btn-block"> Custom </span>' !!}
					@endif
				</td>
				<td class="text-center">
					@if(Hash::check(12345678, $pengguna->password) || Hash::check($pengguna->default_password, $pengguna->password))
					-
					@else 
					<a href="{{route('user.reset_password', ['id' => $pengguna->user_id])}}" class="confirm btn btn-sm btn-danger">Atur Ulang Sandi</a>
					@endif
				</td>
			</tr>
			@endforeach
		</tbody>
	</table>
@endsection

@section('js')
@include('sweet::alert')
<script>
	$('a.confirm').bind('click',function(e) {
		var ini = $(this).parents('tr');
		e.preventDefault();
		var url = $(this).attr('href');
		swal({
			title: "Apakah Anda yakin?",
			text: "Tindakan ini tidak dapat dikembalikan!",
			icon: "warning",
			buttons: true,
			dangerMode: true,
			closeOnClickOutside: false,
		}).then((willDelete) => {
			if (willDelete) {
				$.get(url).done(function(data) {
					swal({
						title:data.title,
						text:data.text, 
						icon: data.icon
					}).then((result) => {
						window.location.replace('{{route('password_siswa')}}');
					});
				});
			}
		});
	});
</script>
@endsection