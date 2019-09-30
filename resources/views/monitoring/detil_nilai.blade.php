@extends('adminlte::page')
@section('title_postfix', 'Monitoring Prestasi Individu Peserta Didik | ')

@section('content_header')
    <h1>Monitoring Prestasi Individu Peserta Didik</h1>
@stop
@section('content')
	<div class="box-header">
		<i class="fa fa-hand-o-right"></i>
		<h3 class="box-title">Detil Nilai Pengetahuan Mata Pelajaran {{$pembelajaran->nama_mata_pelajaran}}</h3>
	</div>
    <div class="box-body">
		<table class="table table-bordered table-hover">
			<tr>
				<th width="5%" class="text-center">ID KD</th>
				<th width="85%">Kompetensi Dasar</th>
				<th width="10%" class="text-center">Rerata Nilai</th>
			</tr>
			@if($pembelajaran->kd_nilai_p->count())
			<?php $jumlah = 0; ?>
			@foreach($pembelajaran->kd_nilai_p as $kd_nilai_p)
			<tr>
				<td class="text-center">{{$kd_nilai_p->kompetensi_dasar->id_kompetensi}}</td>
				<td>{{($kd_nilai_p->kompetensi_dasar->kompetensi_dasar_alias) ? $kd_nilai_p->kompetensi_dasar->kompetensi_dasar_alias : $kd_nilai_p->kompetensi_dasar->kompetensi_dasar}}</td>
				<td class="text-center"><strong>{{number_format($kd_nilai_p->nilai_kd_pengetahuan->avg('nilai_kd'),0)}}</strong></td>
			</tr>
			<?php $jumlah += number_format($kd_nilai_p->nilai_kd_pengetahuan->avg('nilai_kd'),0); ?>
			@endforeach
			<tr>
				<td colspan="2" class="text-right"><strong>Rerata Akhir = </strong></td>
				<td class="text-center"><strong>{{number_format(($jumlah / $pembelajaran->kd_nilai_p->count()),0)}}</strong></td>
			</tr>
			@else
			<tr>
				<td colspan="3" class="text-center">Belum ada penilaian</td>
			</tr>
			@endif
		</table>
	</div>
	<div class="box-header">
		<i class="fa fa-hand-o-right"></i>
		<h3 class="box-title">Detil Nilai Keterampilan Mata Pelajaran {{$pembelajaran->nama_mata_pelajaran}}</h3>
	</div>
    <div class="box-body">
		<table class="table table-bordered table-hover">
			<tr>
				<th width="5%" class="text-center">ID KD</th>
				<th width="85%">Kompetensi Dasar</th>
				<th width="10%" class="text-center">Rerata Nilai</th>
			</tr>
			@if($pembelajaran->kd_nilai_k->count())
			<?php $jumlah = 0; ?>
			@foreach($pembelajaran->kd_nilai_k as $kd_nilai_k)
			<tr>
				<td class="text-center">{{$kd_nilai_k->kompetensi_dasar->id_kompetensi}}</td>
				<td>{{($kd_nilai_k->kompetensi_dasar->kompetensi_dasar_alias) ? $kd_nilai_k->kompetensi_dasar->kompetensi_dasar_alias : $kd_nilai_k->kompetensi_dasar->kompetensi_dasar}}</td>
				<td class="text-center"><strong>{{number_format($kd_nilai_k->nilai_kd_pengetahuan->avg('nilai_kd'),0)}}</strong></td>
			</tr>
			<?php $jumlah += number_format($kd_nilai_k->nilai_kd_keterampilan->avg('nilai_kd'),0); ?>
			@endforeach
			<tr>
				<td colspan="2" class="text-right"><strong>Rerata Akhir = </strong></td>
				<td class="text-center"><strong>{{number_format(($jumlah / $pembelajaran->kd_nilai_k->count()),0)}}</strong></td>
			</tr>
			@else
			<tr>
				<td colspan="3" class="text-center">Belum ada penilaian</td>
			</tr>
			@endif
		</table>
	</div>
@stop