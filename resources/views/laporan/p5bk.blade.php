@extends('adminlte::page')

@section('title_postfix', 'Cetak Rapor P5BK |')

@section('content_header')
    <h1>Cetak Rapor P5BK</h1>
@stop

@section('content')
<div class="table-responsive no-padding">
	<table class="table table-bordered">
		<thead>
			<tr>
				<th width="30%" style="vertical-align:middle;">Nama Peserta Didik</th>
				<th width="10%" style="vertical-align:middle;" class="text-center">Lihat Nilai</th>
				<th width="10%" style="vertical-align:middle;" class="text-center">Rapor P5BK</th>
			</tr>
		</thead>
		<tbody>
			@foreach($get_siswa as $siswa)
			<tr>
				<td>{{strtoupper($siswa->siswa->nama)}}</td>
				<td class="text-center">
					<a href="{{url('laporan/review-p5bk/'.$siswa->anggota_rombel_id)}}" target="_blank" class="btn btn-social-icon btn-dropbox tooltip-left" title="Lihat nilai {{strtoupper($siswa->siswa->nama)}}">
						<i class="fa fa-search"></i></a>
				</td>
				<td class="text-center">
					<a href="{{url('cetak/rapor-p5bk/'.$siswa->anggota_rombel_id)}}" target="_blank" class="btn btn-social-icon btn-foursquare tooltip-left" title="Cetak rapor {{strtoupper($siswa->siswa->nama)}}">
					<i class="fa fa-fw fa-file-pdf-o"></i></a>
				</td>
			</tr>
			{{--dd($siswa)--}}
			@endforeach
		</tbody>
	</table>
</div>
@stop