<?php
if($rombongan_belajar->kunci_nilai){
?>
<script>
	$('#simpan').remove();
</script>
<div class="alert alert-danger alert-dismissible">
	<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
	<h4><i class="icon fa fa-info"></i> Informasi!</h4>
	Status penilaian tidak aktif. Untuk menambah atau memperbaharui penilaian remedial, silahkan menghubungi wali kelas.
</div>
<?php
} else {
?>
<script>
	$('#simpan').show();
</script>
<table class="table table-bordered table-hover" id="remedial">
	<thead>
		<tr>
			<th class="text-center" width="20%">Nama Peserta Didik</th>
			<th class="text-center" width="10%">Nilai Akhir</th>
			<th class="text-center" width="10%">Nilai CP</th>
			<th class="text-center" width="60%">Capaian Kompetensi</th>
		</tr>
	</thead>
	<tbody>
		@foreach($all_siswa as $siswa)
			<tr>
				<td rowspan="{{$siswa->nilai_kd_pk->count() + 1}}" style="vertical-align: middle;">
					{{strtoupper($siswa->siswa->nama)}}
					<input type="hidden" name="siswa_id[{{$siswa->anggota_rombel_id}}]" value="{{$siswa->anggota_rombel_id}}" />
				</td>
				@if ($siswa->nilai_kd_pk->count())
				<td rowspan="{{$siswa->nilai_kd_pk->count() + 1}}" class="text-center" style="vertical-align: middle;">{{($siswa->nilai_rapor_pk) ? $siswa->nilai_rapor_pk->nilai_k : 0}}</td>
				@else
				<td class="text-center">-</td>
				<td class="text-center">-</td>
				<td class="text-center">-</td>
				@endif
			</tr>
			@foreach ($siswa->nilai_kd_pk as $item)
			<?php
			$deskripsi_pengetahuan = $siswa->deskripsi_mata_pelajaran()->where('kompetensi_dasar_id', $item->kd_nilai->kompetensi_dasar->kompetensi_dasar_id)->first();
			?>
			<tr>
				<td class="text-center">
					{{$item->nilai_kd}}
					<input type="hidden" name="nilai_kd[{{$siswa->anggota_rombel_id}}]" value="{{$item->nilai_kd}}" />
					<input type="hidden" name="kompetensi_dasar_id[{{$siswa->anggota_rombel_id}}]" value="{{$item->kd_nilai->kompetensi_dasar->kompetensi_dasar_id}}" />
				</td>
				<td class="text-center">
					<textarea name="deskripsi_pengetahuan[{{$siswa->anggota_rombel_id}}][{{$item->kd_nilai->kompetensi_dasar->kompetensi_dasar_id}}]" class="form-control" rows="5">{{($deskripsi_pengetahuan) ? $deskripsi_pengetahuan->deskripsi_pengetahuan : $item->kd_nilai->kompetensi_dasar->kompetensi_dasar}}</textarea>
				</td>	
			</tr>
			@endforeach
		@endforeach
	</tbody>
</table>
<?php 
}
?>