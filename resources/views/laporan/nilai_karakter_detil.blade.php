@extends('layouts.modal')

@section('title') Detil Nilai Karakter @stop

@section('content')
	<table class="table table-bordered">
		<tr>
			<td width="25%">Nama Peserta Didik</td>
			<td width="1%" class="text-center">:</td>
			<td width="74%">{{strtoupper($data->siswa->nama)}}</td>
		</tr>
		<tr>
			<td style="vertical-align:middle;">Catatan Perkembangan Karakter</td>
			<td style="vertical-align:middle;">:</td>
			<td>{{$data->capaian}}</td>
		</tr>
		@foreach($data->nilai_karakter as $nilai_karakter)
		<tr>
			<td style="vertical-align:middle;">Catatan Penilaian Sikap {{$nilai_karakter->sikap->butir_sikap}}</td>
			<td style="vertical-align:middle;">:</td>
			<td>{{$nilai_karakter->deskripsi}}</td>
		</tr>
		@endforeach
	</table>
@endsection
@section('footer')
    <a class="btn btn-default" href="javascript:void(0)" data-dismiss="modal">Tutup</a>
@endsection