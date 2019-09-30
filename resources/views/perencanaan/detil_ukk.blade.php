@extends('layouts.modal')

@section('title') Detil Perencanaan Penilaian UKK @stop
@section('content')
<table class="table" style="width:50%">
	<tr>
		<td width="30%">Kompetensi Keahlian</td>
		<td width="1px">:</td>
		<td width="70%">{{$rencana_ukk->paket_ukk->jurusan->nama_jurusan}}</td>
	</tr>
	<tr>
		<td>Kurikulum</td>
		<td>:</td>
		<td>{{$rencana_ukk->paket_ukk->kurikulum->nama_kurikulum}}</td>
	</tr>
	<tr>
		<td>Paket Kompetensi</td>
		<td>:</td>
		<td>{{$rencana_ukk->paket_ukk->nama_paket_id}}</td>
	</tr>
	<tr>
		<td>Penguji Internal</td>
		<td>:</td>
		<td>{{($rencana_ukk->guru_internal) ? CustomHelper::nama_guru($rencana_ukk->guru_internal->gelar_depan, $rencana_ukk->guru_internal->nama, $rencana_ukk->guru_internal->gelar_belakang) : '-'}}</td>
	</tr>
	<tr>
		<td>Penguji Eksternal</td>
		<td>:</td>
		<td>
		@if($rencana_ukk->guru_eksternal->dudi)
			{{CustomHelper::nama_guru($rencana_ukk->guru_eksternal->gelar_depan, $rencana_ukk->guru_eksternal->nama, $rencana_ukk->guru_eksternal->gelar_belakang).' ('.$rencana_ukk->guru_eksternal->dudi->nama.')'}}
		@else
			{{($rencana_ukk->guru_eksternal) ? CustomHelper::nama_guru($rencana_ukk->guru_eksternal->gelar_depan, $rencana_ukk->guru_eksternal->nama, $rencana_ukk->guru_eksternal->gelar_belakang) : '-'}}
		@endif
		</td>
	</tr>
</table>
{{--dd($rencana_ukk->paket_ukk)--}}
<h4>Data Peserta Ujian Kompetensi</h4>
<table class="table table-bordered table-striped table-hover">
	<thead>
		<tr>
			<th width="5%" class="text-center">No.</th>
			<th width="50%">Nama Peserta Didik</th>
			<th width="50%" class="text-center">Rombongan Belajar</th>
		</tr>
	</thead>
	<tbody>
	@foreach($data_siswa as $siswa)
		<tr>
			<td class="text-center">{{$loop->iteration}}</td>
			<td>{{strtoupper($siswa->siswa->nama)}}</td>
			<td>{{$siswa->rombongan_belajar->nama}}</td>
		</tr>
	@endforeach
	</tbody>
</table>
@stop