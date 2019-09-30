@extends('adminlte::page')

@section('title_postfix', 'Pratinjau Nilai |')

@section('content_header')
    <h1>Pratinjau Nilai {{strtoupper($get_siswa->siswa->nama)}}</h1>
@stop

@section('content')
<table class="table table-bordered table-striped">
	<thead>
		<tr>
			<th style="vertical-align:middle;" align="center" rowspan="2">No</th>
			<th style="vertical-align:middle;" rowspan="2">Mata Pelajaran</th>
			<th align="center" class="text-center" rowspan="2">SKM</th>
			<th colspan="2" align="center" class="text-center">Pengetahuan</th>
			<th colspan="2" align="center" class="text-center">Keterampilan</th>
		</tr>
		<tr>	
			<th align="center" class="text-center">Angka</th>
			<th align="center" class="text-center">Huruf</th>
			<th align="center" class="text-center">Angka</th>
			<th align="center" class="text-center">Huruf</th>
		</tr>
	</thead>
	<tbody>
	@foreach($get_siswa->rombongan_belajar->pembelajaran as $pembelajaran)
	<?php
	$all_pembelajaran[$pembelajaran->kelompok->nama_kelompok][] = array(
		'nama_mata_pelajaran'	=> $pembelajaran->nama_mata_pelajaran,
		'kkm'	=> CustomHelper::get_kkm($pembelajaran->kelompok_id, $pembelajaran->kkm),
		'nilai_akhir_pengetahuan'	=> ($pembelajaran->nilai_akhir_pengetahuan) ? $pembelajaran->nilai_akhir_pengetahuan->nilai : 0,
		'huruf_pengetahuan'	=> ($pembelajaran->nilai_akhir_pengetahuan) ? CustomHelper::terbilang($pembelajaran->nilai_akhir_pengetahuan->nilai) : '-',
		'nilai_akhir_keterampilan'	=> ($pembelajaran->nilai_akhir_keterampilan) ? $pembelajaran->nilai_akhir_keterampilan->nilai : 0,
		'huruf_keterampilan'	=> ($pembelajaran->nilai_akhir_keterampilan) ? CustomHelper::terbilang($pembelajaran->nilai_akhir_keterampilan->nilai) : '-',
	);
	$i=1;
	?>
	@endforeach
	@foreach($all_pembelajaran as $kelompok => $data_pembelajaran)
	<tr>
		<td colspan="7"><b style="font-size: 13px;">{{$kelompok}}</b></td>
	</tr>
	@foreach($data_pembelajaran as $pembelajaran)
	<?php $pembelajaran = (object) $pembelajaran; ?>
		<tr>
			<td class="text-center">{{$i++}}</td>
			<td>{{$pembelajaran->nama_mata_pelajaran}}</td>
			<td class="text-center">{{$pembelajaran->kkm}}</td>
			<td class="text-center">{{$pembelajaran->nilai_akhir_pengetahuan}}</td>
			<td class="text-center">{{$pembelajaran->huruf_pengetahuan}}</td>
			<td class="text-center">{{$pembelajaran->nilai_akhir_keterampilan}}</td>
			<td class="text-center">{{$pembelajaran->huruf_keterampilan}}</td>
		</tr>
	@endforeach
	@endforeach
	</tbody>
</table>
@stop