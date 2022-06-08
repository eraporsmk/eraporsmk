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
<?php
if($get_siswa->rombongan_belajar->tingkat == 10){
	$huruf_ekskul = 'C';
	$huruf_absen = 'D';
	$huruf_kenaikan = 'E';
} else {
	$huruf_ekskul = 'D';
	$huruf_absen = 'E';
	$huruf_kenaikan = 'F';
}
?>
@if($get_siswa->rombongan_belajar->tingkat != 10)
<div class="strong">C.&nbsp;&nbsp;Praktik Kerja Lapangan</div>
<table border="1" class="table">
	<thead>
		<tr>
			<th style="width: 2px;" align="center">No</th>
			<th style="width: 300px;" align="center">Mitra DU/DI</th>
			<th style="width: 200px;" align="center">Lokasi</th>
			<th style="width: 100px;" align="center">Lamanya<br>(bulan)</th>
			<th style="width: 100px;" align="center">Keterangan</th>
		</tr>
	</thead>
	<tbody>
		@if($get_siswa->all_prakerin->count())
		@foreach($get_siswa->all_prakerin as $prakerin){
		<tr>
			<td align="center">{{$loop->iteration}}</td>
			<td>{{$prakerin->mitra_prakerin}}</td>
			<td align="center">{{$prakerin->lokasi_prakerin}}</td>
			<td align="center">{{$prakerin->lama_prakerin}}</td>
			<td>{{$prakerin->keterangan_prakerin}}</td>
		</tr>
		@endforeach
		@else
		<tr>
			<td class="text-center" colspan="5">&nbsp;</td>
		</tr>
		@endif
	</tbody>
</table>
<br />
@endif
<div class="strong">{{$huruf_ekskul}}.&nbsp;&nbsp;Ekstrakurikuler</div>
<table border="1" class="table">
	<thead>
		<tr>
			<th style="width: 5%;" align="center">No</th>
			<th style="width: 35%;" align="center">Kegiatan Ekstrakurikuler</th>
			<th style="width: 60%;" align="center">Keterangan</th>
		</tr>
	</thead>
	<tbody>
		@if($get_siswa->all_nilai_ekskul->count())
		@foreach($get_siswa->all_nilai_ekskul as $nilai_ekskul)
		<tr>
			<td align="center">{{$loop->iteration}}</td>
			<td>{{strtoupper($nilai_ekskul->rombongan_belajar->nama)}}</td>
			<td>{{$nilai_ekskul->deskripsi_ekskul}}</td>
		</tr>
		@endforeach
		@else
		<tr>
			<td class="text-center" colspan="3">&nbsp;</td>
		</tr>
		@endif
	</tbody>
</table>
<br />
<div class="strong">{{$huruf_absen}}.&nbsp;&nbsp;Ketidakhadiran</div>
<table border="1" width="500">
	<tr>
	<tr>
		<td width="200">Sakit</td>
		<td> : {{($get_siswa->kehadiran) ? ($get_siswa->kehadiran->sakit) ? $get_siswa->kehadiran->sakit.' hari' : '- hari'
			: '.... hari'}}</td>
	</tr>
	<tr>
		<td>Izin</td>
		<td width="300"> : {{($get_siswa->kehadiran) ? ($get_siswa->kehadiran->izin) ? $get_siswa->kehadiran->izin.' hari' :
			'- hari' : '.... hari'}}</td>
	</tr>
	<tr>
		<td>Tanpa Keterangan</td>
		<td> : {{($get_siswa->kehadiran) ? ($get_siswa->kehadiran->alpa) ? $get_siswa->kehadiran->alpa.' hari' : '- hari' :
			'.... hari'}}</td>
	</tr>
	</tr>
</table>
<br />
<?php
/*if($cari_tingkat_akhir && !in_array($get_siswa->rombongan_belajar_id, $rombel_4_tahun)){
	$text_status = 'Status Kelulusan';
	$not_yet = 'Belum dilakukan kelulusan';
} else*/
if($get_siswa->rombongan_belajar->semester->semester == 2){
	if($get_siswa->rombongan_belajar->tingkat == 12){
		$text_status = 'Status Kelulusan';
		$not_yet = 'Belum dilakukan kelulusan';
	} else {
		$text_status = 'Kenaikan Kelas';
		$not_yet = 'Belum dilakukan kenaikan kelas';
	}
} else {
	$text_status = '';
	$not_yet = '';
}
?>
@if($get_siswa->rombongan_belajar->semester->semester == 2)
@if($get_siswa->rombongan_belajar->tingkat == 12)
<div class="strong">{{$huruf_kenaikan}}.&nbsp;&nbsp;{{$text_status}}</div>
@else
<div class="strong">{{$huruf_kenaikan}}.&nbsp;&nbsp;{{$text_status}}</div>
@endif
@endif
@if($get_siswa->rombongan_belajar->semester->semester == 2)
<table width="100%" border="1">
	<tr>
		<td style="padding:10px;">
			@if($get_siswa->kenaikan)
			@if($get_siswa->kenaikan->status == 3)
			LULUS
			@else
			{{CustomHelper::status_kenaikan($get_siswa->kenaikan->status)}} {{$get_siswa->kenaikan->nama_kelas}}
			@endif
			@else
			{{$not_yet}}
			@endif
		</td>
	</tr>
</table>
<br>
@endif
<br>
<table width="100%">
	<tr>
		<td style="width:40%">
			<p>Orang Tua/Wali</p><br>
			<br>
			<br>
			<br>
			<br>
			<br>
			<p>...................................................................</p>
		</td>
		<td style="width:20%"></td>
		<td style="width:40%">
			<p>{{str_replace('Kab. ','',$get_siswa->sekolah->kabupaten)}},
				{{CustomHelper::TanggalIndo($tanggal_rapor)}}<br>Wali Kelas</p><br>
			<br>
			<br>
			<br>
			<br>
			<br>
			<p>
				<u>{{ CustomHelper::nama_guru($get_siswa->rombongan_belajar->wali->gelar_depan,
					$get_siswa->rombongan_belajar->wali->nama, $get_siswa->rombongan_belajar->wali->gelar_belakang) }}</u><br />
				NIP. {{$get_siswa->rombongan_belajar->wali->nip}}
		</td>
	</tr>
</table>
<table width="100%" style="margin-top:10px;">
	<tr>
		<td style="width:40%;">
		</td>
		<td style="width:60%;">
			<p>Mengetahui,<br>Kepala Sekolah</p>
			<br>
			<br>
			<br>
			<br>
			<br>
			<p><u>{{ CustomHelper::nama_guru($get_siswa->sekolah->guru->gelar_depan, $get_siswa->sekolah->guru->nama,
					$get_siswa->sekolah->guru->gelar_belakang) }}</u><br />
				NIP. {{$get_siswa->sekolah->guru->nip}}
			</p>
		</td>
	</tr>
</table>
@endsection