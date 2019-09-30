@extends('adminlte::page')

@section('title_postfix', 'Cetak Rapor Semester |')

@section('content_header')
    <h1>Cetak Rapor Semester</h1>
@stop

@section('content')
<div class="table-responsive no-padding">
	<table class="table table-bordered">
		<thead>
			<tr>
			<th width="30%" style="vertical-align:middle;">Nama Peserta Didik</th>
				<th width="10%" style="vertical-align:middle;" class="text-center">Lihat Nilai</th>
				<!--th width="10%" style="vertical-align:middle;" class="text-center">Lihat Deskripsi</th-->
				<th width="10%" style="vertical-align:middle;" class="text-center">Halaman Depan</th>
				<th width="10%" style="vertical-align:middle;" class="text-center">Rapor Akademik &amp; Karakter</th>
				<th width="10%" style="vertical-align:middle;" class="text-center">Dokumen Pendukung</th>
			</tr>
		</thead>
		<tbody>
			@foreach($get_siswa as $siswa)
			<tr>
				<td>{{strtoupper($siswa->siswa->nama)}}</td>
				<td class="text-center">
					<a href="{{url('laporan/review-nilai/1/'.$siswa->anggota_rombel_id)}}" target="_blank" class="btn btn-social-icon btn-dropbox tooltip-left" title="Lihat nilai {{strtoupper($siswa->siswa->nama)}}">
						<i class="fa fa-search"></i></a>
				</td>
				<!--td class="text-center">
					<a href="{{url('laporan/review-desc/1/'.$siswa->anggota_rombel_id)}}" target="_blank" class="btn btn-social-icon btn-flickr tooltip-left" title="Lihat deskripsi {{strtoupper($siswa->siswa->nama)}}">
						<i class="fa fa-search"></i>
					</a>
				</td-->
				<td class="text-center">
					<a href="{{url('cetak/rapor-top/1/'.$siswa->anggota_rombel_id)}}" target="_blank" class="btn btn-social-icon btn-foursquare tooltip-left" title="Cetak rapor {{strtoupper($siswa->siswa->nama)}}">
					<i class="fa fa-fw fa-file-pdf-o"></i></a>
				</td>
				<td class="text-center">
					<a href="{{url('cetak/rapor-nilai/1/'.$siswa->anggota_rombel_id)}}" target="_blank" class="btn btn-social-icon btn-github tooltip-left" title="Cetak rapor {{strtoupper($siswa->siswa->nama)}}">
					<i class="fa fa-fw fa-file-pdf-o"></i></a>
				</td>
				<td class="text-center">
					<a href="{{url('cetak/rapor-pendukung/1/'.$siswa->anggota_rombel_id)}}" target="_blank" class="btn btn-social-icon btn-google tooltip-left" title="Cetak rapor {{strtoupper($siswa->siswa->nama)}}">
					<i class="fa fa-fw fa-file-pdf-o"></i></a>
				</td>
			</tr>
			{{--dd($siswa)--}}
			@endforeach
		</tbody>
	</table>
</div>
@stop