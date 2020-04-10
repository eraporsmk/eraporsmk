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
	<?php
	$all_pembelajaran = array();
	$get_pembelajaran = [];
	$set_pembelajaran = $get_siswa->rombongan_belajar->pembelajaran;//()->whereNotNull('kelompok_id')->orderBy('kelompok_id', 'asc')->orderBy('no_urut', 'asc')->get();
	foreach($set_pembelajaran as $pembelajaran){
		if(in_array($pembelajaran->mata_pelajaran_id, CustomHelper::mapel_agama())){
			if(CustomHelper::filter_pembelajaran_agama($get_siswa->siswa->agama->nama, $pembelajaran->nama_mata_pelajaran)){
				$get_pembelajaran[$pembelajaran->pembelajaran_id] = $pembelajaran;
			}
		} else {
			$get_pembelajaran[$pembelajaran->pembelajaran_id] = $pembelajaran;
		}
	}
	?>
	@foreach($get_pembelajaran as $pembelajaran)
	<?php
	$rasio_p = ($pembelajaran->rasio_p) ? $pembelajaran->rasio_p : 50;
	$rasio_k = ($pembelajaran->rasio_k) ? $pembelajaran->rasio_k : 50;
	$nilai_pengetahuan_value = ($pembelajaran->nilai_akhir_pengetahuan) ? $pembelajaran->nilai_akhir_pengetahuan->nilai : 0;
	$nilai_keterampilan_value = ($pembelajaran->nilai_akhir_keterampilan) ? $pembelajaran->nilai_akhir_keterampilan->nilai : 0;
	$nilai_akhir_pengetahuan	= $nilai_pengetahuan_value * $rasio_p;
	$nilai_akhir_keterampilan	= $nilai_keterampilan_value * $rasio_k;
	$nilai_akhir				= ($nilai_akhir_pengetahuan + $nilai_akhir_keterampilan) / 100;
	$nilai_akhir				= ($nilai_akhir) ? number_format($nilai_akhir,0) : 0;
	$kkm = CustomHelper::get_kkm($pembelajaran->kelompok_id, $pembelajaran->kkm);
	$produktif = array(4,5,9,10,13);
	if(in_array($pembelajaran->kelompok_id,$produktif)){
		$produktif = 1;
	} else {
		$produktif = 0;
	}
	//$get_mapel_agama = CustomHelper::filter_agama_siswa($pembelajaran->pembelajaran_id, $pembelajaran->rombongan_belajar_id);
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