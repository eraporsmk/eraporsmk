@extends('adminlte::page')

@section('title_postfix', 'Data Nilai Ekstrakurikuler |')

@section('content_header')
    <h1>Data Nilai Ekstrakurikuler</h1>
@stop

@section('content')
	<div class="table-responsive no-padding">
		<table class="datatable table table-bordered">
			<thead>
				<tr>
					<th width="20%" class="text-center">Nama Peserta Didik</th>
					<th width="20%" class="text-center">Nama Eskul</th>
					<th width="20%" class="text-center">Pembina</th>
					<th width="10%" class="text-center">Predikat</th>
					<th width="30%" class="text-center">Deskripsi</th>
				</tr>
			</thead>
			<tbody>
				@foreach($get_siswa as $siswa)
				<tr>
					<td rowspan="{{$siswa->anggota_ekskul->count() + 1}}" style="vertical-align:middle;">
						{{strtoupper($siswa->siswa->nama)}}
					</td>
					@if($siswa->anggota_ekskul->count())
					@foreach($siswa->anggota_ekskul as $anggota_ekskul)
				<tr>
					<td style="vertical-align:middle;">{{$anggota_ekskul->kelas_ekskul->nama}}</td>
					<td style="vertical-align:middle;">{{CustomHelper::nama_guru($anggota_ekskul->kelas_ekskul->wali->gelar_depan, $anggota_ekskul->kelas_ekskul->wali->nama, $anggota_ekskul->kelas_ekskul->wali->gelar_belakang)}}</td>
					<td>
						<?php
						if ($anggota_ekskul->nilai_ekskul){
							if($anggota_ekskul->nilai_ekskul->nilai == 1){
								$nilai = 'Sangat Baik';
							}elseif($anggota_ekskul->nilai_ekskul->nilai == 2){
								$nilai = 'Baik';
							}elseif($anggota_ekskul->nilai_ekskul->nilai == 3){
								$nilai = 'Cukup';
							}elseif($anggota_ekskul->nilai_ekskul->nilai == 4){
								$nilai = 'Kurang';
							}else{
								$nilai = '-';
							}
						} else {
							$nilai = '-';
						}
						?>
						{{$nilai}}
					</td>
					<td>
						{{($anggota_ekskul->nilai_ekskul) ? $anggota_ekskul->nilai_ekskul->deskripsi_ekskul : '-'}}
					</td>
				</tr>
					@endforeach
					@else
					<td>-</td>
					<td>-</td>
					@endif
				</tr>
				@endforeach
			</tbody>
		</table>
	</div>
@stop