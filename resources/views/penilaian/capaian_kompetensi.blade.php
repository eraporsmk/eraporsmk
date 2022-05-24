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
			<td rowspan="2" style="vertical-align: middle;">
				{{strtoupper($siswa->siswa->nama)}}
				<input type="hidden" name="siswa_id[{{$siswa->anggota_rombel_id}}]" value="{{$siswa->anggota_rombel_id}}" />
				<input type="hidden" name="nilai_akhir[{{$siswa->anggota_rombel_id}}]" value="{{($siswa->nilai_rapor_pk) ? $siswa->nilai_rapor_pk->nilai_k : 0}}" />
			</td>
			<td rowspan="2" style="vertical-align: middle;" class="text-center">{{($siswa->nilai_rapor_pk) ? $siswa->nilai_rapor_pk->nilai_k : 0}}</td>
			<td class="text-center">Nilai CP Tertinggi:<br>{{($siswa->nilai_kd_pk_tertinggi) ? $siswa->nilai_kd_pk_tertinggi->nilai_kd : 0}}</td>
			<td>
			<?php
			$deskripsi_tertinggi = ($siswa->nilai_kd_pk_tertinggi) ? $siswa->deskripsi_mata_pelajaran()->where('pembelajaran_id', $pembelajaran_id)->where('kompetensi_dasar_id', $siswa->nilai_kd_pk_tertinggi->kd_nilai->kompetensi_dasar->kompetensi_dasar_id)->first() : '';
			$kompetensi_dasar_id_tertinggi = ($siswa->nilai_kd_pk_tertinggi) ? $siswa->nilai_kd_pk_tertinggi->kd_nilai->kompetensi_dasar->kompetensi_dasar_id : '';
			$kompetensi_dasar_tertinggi = ($siswa->nilai_kd_pk_tertinggi) ? CustomHelper::limit_text($siswa->nilai_kd_pk_tertinggi->kd_nilai->kompetensi_dasar->kompetensi_dasar) : '';
			?>
			<textarea name="deskripsi_pengetahuan[{{$siswa->anggota_rombel_id}}][{{$kompetensi_dasar_id_tertinggi}}]" class="textarea form-control" rows="5">{{($deskripsi_tertinggi) ? $deskripsi_tertinggi->deskripsi_pengetahuan : $kompetensi_dasar_tertinggi}}</textarea>
			</td>
		</tr>
		<tr>
			<td class="text-center">Nilai CP Terendah:<br>{{($siswa->nilai_kd_pk_terendah) ? $siswa->nilai_kd_pk_terendah->nilai_kd : 0}}</td>
			<td>
			<?php
			$deskripsi_terendah = ($siswa->nilai_kd_pk_terendah) ? $siswa->deskripsi_mata_pelajaran()->where('pembelajaran_id', $pembelajaran_id)->where('kompetensi_dasar_id', $siswa->nilai_kd_pk_terendah->kd_nilai->kompetensi_dasar->kompetensi_dasar_id)->first() : '';
			$kompetensi_dasar_id_terendah = ($siswa->nilai_kd_pk_terendah) ? $siswa->nilai_kd_pk_terendah->kd_nilai->kompetensi_dasar->kompetensi_dasar_id : '';
			$kompetensi_dasar_terendah = ($siswa->nilai_kd_pk_terendah) ? CustomHelper::limit_text($siswa->nilai_kd_pk_terendah->kd_nilai->kompetensi_dasar->kompetensi_dasar) : '';
			?>
			<textarea name="deskripsi_pengetahuan[{{$siswa->anggota_rombel_id}}][{{$kompetensi_dasar_id_terendah}}]" class="textarea form-control" rows="5">{{($deskripsi_terendah) ? $deskripsi_terendah->deskripsi_pengetahuan : $kompetensi_dasar_terendah}}</textarea>
			</td>
		</tr>
		@endforeach
	</tbody>
	<?php
	/*
	<tbody>
		@foreach($all_siswa as $siswa)
		{{dump($siswa->nilai_kd_pk_tertinggi)}}
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
	*/
	?>
</table>
<?php 
}
?>
<link rel="stylesheet" href="{{ asset('vendor/adminlte/plugins/bootstrap-wysihtml5/bootstrap3-wysihtml5.min.css') }}">
<script src="{{ asset('vendor/adminlte/plugins/bootstrap-wysihtml5/bootstrap3-wysihtml5.all.min.js') }}"></script>
<script>
	$('.textarea').wysihtml5();
</script>