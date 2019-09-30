<?php
$kelompok_produktif = array(4, 5, 9, 10, 13);
$mapel_produktif = NULL;
if(in_array($pembelajaran->kelompok_id, $kelompok_produktif)){
	$mapel_produktif = 1;
}
?>
<table border="1">
	<thead>
		<tr>
			<th>No</th>
			<th>Nama</th>
			<th>Nilai Pengetahuan</th>
			<th>Nilai Keterampilan</th>
			<th>Nilai Akhir</th>
			<th>Predikat</th>
		</tr>
	</thead>
	<tbody>
	@if($pembelajaran->anggota_rombel->count())
	@foreach($pembelajaran->anggota_rombel as $anggota_rombel)
	<?php 
	$nilai_pengetahuan = '-';
	$nilai_keterampilan = '-';
	$nilai_akhir_pengetahuan = 0;
	$nilai_akhir_keterampilan = 0;
	if($anggota_rombel->nilai_akhir_pengetahuan){
		$nilai_pengetahuan = $anggota_rombel->nilai_akhir_pengetahuan->nilai;
		$nilai_akhir_pengetahuan = ($nilai_pengetahuan * $rasio_p);
	}
	if($anggota_rombel->nilai_akhir_keterampilan){
		$nilai_keterampilan = $anggota_rombel->nilai_akhir_keterampilan->nilai;
		$nilai_akhir_keterampilan = ($nilai_keterampilan * $rasio_k);
	}
	$nilai_akhir = ($nilai_akhir_pengetahuan + $nilai_akhir_keterampilan) / 100;
	$nilai_akhir = ($nilai_akhir) ? number_format($nilai_akhir,0) : 0;
	?>
		<tr>
			<td>{{$loop->iteration}}</td>
			<td>{{strtoupper($anggota_rombel->siswa->nama)}}</td>
			<td>{{$nilai_pengetahuan}}</td>
			<td>{{$nilai_keterampilan}}</td>
			<td>{{$nilai_akhir}}</td>
			<td>{{CustomHelper::konversi_huruf(CustomHelper::get_kkm($pembelajaran->kelompok_id, 0),$nilai_akhir, $mapel_produktif)}}</td>
		</tr>
	@endforeach
	@else
		<tr>
			<td colspan="6">Tidak ada data untuk ditampilkan</td>
		</tr>
	@endif
	</tbody>
</table>