<a href="{{url('monitoring/unduh-nilai/'.$pembelajaran->pembelajaran_id)}}" class="btn btn-success pull-right"><i class="fa fa-file-excel-o"></i> Unduh Rekap</a>
<h3>Rekapitulasi Penilaian Mata Pelajaran {{$pembelajaran->nama_mata_pelajaran}}</h3>
<?php
$kelompok_produktif = array(4, 5, 9, 10, 13);
$mapel_produktif = NULL;
if(in_array($pembelajaran->kelompok_id, $kelompok_produktif)){
	$mapel_produktif = 1;
}
?>
<table class="table table-bordered table-striped table-hover jarak1">
	<thead>
		<tr>
			<th class="text-center" width="1%">No</th>
			<th style="width: 40%">Nama</th>
			<th style="width: 10%" class="text-center">Nilai Pengetahuan</th>
			<th style="width: 10%" class="text-center">Nilai Keterampilan</th>
			<th style="width: 10%" class="text-center">Nilai Akhir</th>
			<th style="width: 10%" class="text-center">Predikat</th>
		</tr>
	</thead>
	<tbody>
	@if($pembelajaran->rombongan_belajar->anggota_rombel->count())
	@foreach($pembelajaran->rombongan_belajar->anggota_rombel as $anggota_rombel)
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
			<td class="text-center">{{$loop->iteration}}</td>
			<td>{{strtoupper($anggota_rombel->siswa->nama)}}</td>
			<td class="text-center">{{$nilai_pengetahuan}}</td>
			<td class="text-center">{{$nilai_keterampilan}}</td>
			<td class="text-center">{{$nilai_akhir}}</td>
			<td class="text-center">{{CustomHelper::konversi_huruf(CustomHelper::get_kkm($pembelajaran->kelompok_id, 0),$nilai_akhir, $mapel_produktif)}}</td>
		</tr>
	@endforeach
	@else
		<tr>
			<td class="text-center" colspan="6">Tidak ada data untuk ditampilkan</td>
		</tr>
	@endif
	</tbody>
</table>