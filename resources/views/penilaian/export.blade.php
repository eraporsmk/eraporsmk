<?php
$anggota_rombel = $rencana_penilaian->pembelajaran->anggota_rombel;
$get_kkm = CustomHelper::get_kkm($rencana_penilaian->pembelajaran->kelompok_id, $rencana_penilaian->pembelajaran->kkm);
?>
<table border="1">
	<thead>
		<tr>
			<th colspan="{{3 + $rencana_penilaian->kd_nilai->count()}}">Format Excel Import Nilai eRaporSMK</th>
		</tr>
		<tr>
			<th colspan="2">Aspek Penilaian</th>
			<th colspan="{{1 + $rencana_penilaian->kd_nilai->count()}}">: {{($rencana_penilaian->kompetensi_id == 1) ? 'Pengetahuan' : 'Keterampilan'}}</th>
		</tr>
		<tr>
			<th colspan="2">Aktifitas Penilaian</th>
			<th colspan="{{1 + $rencana_penilaian->kd_nilai->count()}}">: {{$rencana_penilaian->nama_penilaian}}</th>
		</tr>
		<tr>
			<th colspan="2">Mata Pelajaran</th>
			<th colspan="{{1 + $rencana_penilaian->kd_nilai->count()}}">: {{$rencana_penilaian->pembelajaran->nama_mata_pelajaran}}</th>
		</tr>
		<tr>
			<th colspan="2">Rombongan Belajar</th>
			<th colspan="{{1 + $rencana_penilaian->kd_nilai->count()}}">: {{$rencana_penilaian->pembelajaran->rombongan_belajar->nama}}</th>
		</tr>
		<tr>
			<th colspan="2">SKM</th>
			<th colspan="{{1 + $rencana_penilaian->kd_nilai->count()}}">: {{$get_kkm}}</th>
		</tr>
		<tr>
			<td rowspan="2">NO</td>
			<td rowspan="2">NAMA PESERTA DIDIK</td>
			<td rowspan="2">NISN</td>
			<td colspan="{{$rencana_penilaian->kd_nilai->count()}}">NILAI PER KOMPETENSI DASAR</td>
		</tr>
		<tr>
			@foreach($rencana_penilaian->kd_nilai as $kd_nilai)
			<td>kd_{{$kd_nilai->id_kompetensi}}</td>
			@endforeach
		</tr>
	</thead>
	<tbody>
		@foreach($anggota_rombel as $anggota)
		<tr>
			<td>{{$loop->iteration}}</td>
			<td>{{strtoupper($anggota->siswa->nama)}}</td>
			<td>{{$anggota->siswa->nisn}}</td>
			@foreach($rencana_penilaian->kd_nilai as $kd_nilai)
			<td>{{CustomHelper::get_nilai($anggota->anggota_rombel_id, $kd_nilai->kd_nilai_id)}}</td>
			@endforeach
		</tr>
		@endforeach
	</tbody>
</table>