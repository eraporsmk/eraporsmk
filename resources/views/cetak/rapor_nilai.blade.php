@extends('layouts.cetak')
@section('content')
<table border="0" width="100%">
	<tr>
    	<td style="width: 25%;padding-top:5px; padding-bottom:5px; padding-left:0px;">Nama Peserta Didik</td>
		<td style="width: 1%;" class="text-center">:</td>
		<td style="width: 74%">{{strtoupper($get_siswa->siswa->nama)}}</td>
	</tr>
	<tr>
		<td style="padding-top:5px; padding-bottom:5px; padding-left:0px;">Nomor Induk/NISN</td>
		<td class="text-center">:</td>
		<td>{{$get_siswa->siswa->no_induk.' / '.$get_siswa->siswa->nisn}}</td>
	</tr>
	<tr>
		<td style="padding-top:5px; padding-bottom:5px; padding-left:0px;">Kelas</td>
		<td class="text-center">:</td>
		<td>{{$get_siswa->rombongan_belajar->nama}}</td>
	</tr>
	<tr>
		<td style="padding-top:5px; padding-bottom:5px; padding-left:0px;">Tahun Pelajaran</td>
		<td class="text-center">:</td>
		<td>{{str_replace('/','-',substr($get_siswa->rombongan_belajar->semester->nama,0,9))}}</td>
	</tr>
	<tr>
		<td style="padding-top:5px; padding-bottom:5px; padding-left:0px;">Semester</td>
		<td class="text-center">:</td>
		<td>{{substr($get_siswa->rombongan_belajar->semester->nama,10)}}</td>
	</tr>
</table>
<br />
<div class="strong">A.&nbsp;&nbsp;Nilai Akademik</div>
<table class="table" border="1">
	<thead>
		<tr>
			<th style="vertical-align:middle;width: 2px;" align="center">No</th>
			<th style="vertical-align:middle;width: 200px;">Mata Pelajaran</th>
			<th align="center" class="text-center">Pengetahuan</th>
			<th align="center" class="text-center">Keterampilan</th>
			<th align="center" class="text-center">Nilai Akhir</th>
			<th align="center" class="text-center">Predikat</th>
		</tr>
	</thead>
	<tbody>
	<?php
	$all_pembelajaran = array();
	?>
	@foreach($get_siswa->rombongan_belajar->pembelajaran as $pembelajaran)
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
	$get_mapel_agama = CustomHelper::filter_agama_siswa($pembelajaran->pembelajaran_id, $pembelajaran->rombongan_belajar_id);
	$all_pembelajaran[$pembelajaran->kelompok->nama_kelompok][] = array(
		'nama_mata_pelajaran'	=> $pembelajaran->nama_mata_pelajaran,
		'nilai_akhir_pengetahuan'	=> $nilai_pengetahuan_value,
		'nilai_akhir_keterampilan'	=> $nilai_keterampilan_value,
		'nilai_akhir'	=> $nilai_akhir,
		'predikat'	=> CustomHelper::konversi_huruf($kkm, $nilai_akhir, $produktif),
		//CustomHelper::terbilang($nilai_akhir),
	);
	$i=1;
	?>
	@endforeach
	@foreach($all_pembelajaran as $kelompok => $data_pembelajaran)
	<tr>
		<td colspan="6"><b style="font-size: 13px;">{{$kelompok}}</b></td>
	</tr>
	@foreach($data_pembelajaran as $pembelajaran)
	<?php $pembelajaran = (object) $pembelajaran; ?>
		<tr>
			<td class="text-center">{{$i++}}</td>
			<td>{{$pembelajaran->nama_mata_pelajaran}}</td>
			<td class="text-center">{{$pembelajaran->nilai_akhir_pengetahuan}}</td>
			<td class="text-center">{{$pembelajaran->nilai_akhir_keterampilan}}</td>
			<td class="text-center">{{$pembelajaran->nilai_akhir}}</td>
			<td class="text-center">{{$pembelajaran->predikat}}</td>
		</tr>
	@endforeach
	@endforeach
	</tbody>
</table>
<br />
<div class="strong">B.&nbsp;&nbsp;Catatan Akademik</div>
<table width="100%" border="1">
  <tr>
    <td style="padding:10px;">{{($get_siswa->catatan_wali) ? $get_siswa->catatan_wali->uraian_deskripsi : ''}}</td>
  </tr>
</table>
@endsection