@extends('adminlte::page')

@section('title_postfix', 'Pratinjau Nilai P5BK |')

@section('content_header')
    <h1>Pratinjau RAPOR PROJEK PROFIL PELAJAR PANCASILA DAN BUDAYA KERJA {{strtoupper($get_siswa->siswa->nama)}}</h1>
@stop

@section('content')
<table class="table">
	<tr>
		<th style="width: 10%">Nama Sekolah</th>
		<th style="width: 20%">{{$get_siswa->rombongan_belajar->sekolah->nama}}</th>
		<th style="width: 20%"></th>
		<th style="width: 10%">Kelas</th>
		<th style="width: 20%">{{$get_siswa->rombongan_belajar->nama}}</th>
	</tr>
	<tr>
		<th>Program Keahlian</th>
		<th>{{$get_siswa->rombongan_belajar->jurusan->nama_jurusan}}</th>
		<th></th>
		<th>Fase</th>
		<th>-</th>
	</tr>
	<tr>
		<th>Nama Peserta Didik</th>
		<th>{{strtoupper($get_siswa->siswa->nama)}}</th>
		<th></th>
		<th>Tahun Pelajaran</th>
		<th>{{$semester->tahun_ajaran_id}}/{{$semester->tahun_ajaran_id + 1}}</th>
	</tr>
	<tr>
		<th>NISN</th>
		<th>{{$get_siswa->siswa->nisn}}</th>
		<th></th>
		<th></th>
		<th></th>
	</tr>
</table>
<table class="table" style="margin-top: 10px;">
	@foreach ($rencana_budaya_kerja as $item)
	<tr>
		<td><strong>Projek {{$loop->iteration}} | {{$item->nama}}</strong></td>
	</tr>
	<tr>
		<td>{{$item->deskripsi}}</td>
	</tr>
	@endforeach
</table>
<table class="table" style="margin-top: 10px;">
	<tr>
		@foreach ($opsi_budaya_kerja as $opsi)
		<td style="width: 10px"><span class="badge bg-{{$opsi->warna}}">&nbsp;&nbsp;</span></td>
		<td>
			<strong>{{$opsi->kode}}. {{$opsi->nama}}</strong><br>
			{{$opsi->deskripsi}}
		</td>
		@endforeach
	</tr>
</table>
<table class="table table-bordered table-striped" style="margin-top: 10px;">
	<thead>
		<tr>
			<th>Projek Kelas {{$get_siswa->rombongan_belajar->tingkat}}</th>
			@foreach ($budaya_kerja as $budaya)
			<th class="text-center">{{$budaya->aspek}}</th>
			@endforeach
		</tr>
	</thead>
	<tbody>
		@foreach ($rencana_budaya_kerja as $rencana)
		<tr>
			<td>{{$loop->iteration}}. {{$rencana->nama}}</td>
			@foreach ($budaya_kerja as $budaya)
			<?php
			$get_nilai_budaya_kerja = $get_siswa->nilai_budaya_kerja()->whereHas('budaya_kerja', function($query) use($budaya){
				$query->where('budaya_kerja.budaya_kerja_id', $budaya->budaya_kerja_id);
			})->whereHas('rencana_budaya_kerja', function($query) use($rencana){
				$query->where('rencana_budaya_kerja.rencana_budaya_kerja_id', $rencana->rencana_budaya_kerja_id);
			})->get();
			$nilai_budaya_kerja = $get_nilai_budaya_kerja->avg('opsi_id');
			?>
			<td class="text-center">
				{!! CustomHelper::opsi_budaya($nilai_budaya_kerja) !!}
			</td>
			@endforeach
		</tr>
		@endforeach
	</tbody>
</table>
@foreach ($rencana_budaya_kerja as $rencana)
<table class="table table-bordered table-striped" style="margin-top: 10px;">
	<thead>
		<tr>
			<th>Projek {{$loop->iteration}} | {{$rencana->nama}}</th>
			@foreach ($opsi_budaya_kerja as $opsi)
			<th style="width: 100px;" class="text-center">{{$opsi->kode}}</th>
			@endforeach
		</tr>
	</thead>
	<tbody>
		@foreach ($rencana->aspek_budaya_kerja as $item)
			<tr>
				<td colspan="7"><strong>{{$item->budaya_kerja->aspek}}</strong></td>
			</tr>
			@foreach ($item->budaya_kerja->elemen_budaya_kerja as $elemen)
			<tr>
				<td><strong>{{$elemen->elemen}}.</strong> {{$elemen->deskripsi}}</td>
				@foreach ($opsi_budaya_kerja as $opsi)
				<td class="text-center">{!! ($elemen->nilai_budaya_kerja && $elemen->nilai_budaya_kerja->opsi_id == $opsi->opsi_id) ? '&check;' : '' !!}</td>
				@endforeach
			</tr>
			@endforeach
		@endforeach
	</tbody>
</table>
@endforeach
<table class="table table-bordered" style="margin-top:10px;">
	<tr>
		<th>Catatan Kegiatan</th>
	</tr>
	<tr>
		<td>{{($get_siswa->catatan_budaya_kerja) ? $get_siswa->catatan_budaya_kerja->catatan : '-'}}</td>
	</tr>
</table>
@stop