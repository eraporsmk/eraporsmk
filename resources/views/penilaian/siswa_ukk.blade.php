<table class="table table-bordered table-striped table-hover">
	<thead>
		<tr>
			<th width="60%">Nama Peserta Didik</th>
			<th width="10%" class="text-center">Nilai</th>
			<th width="20%" class="text-center">Kesimpulan</th>
			<th class="text-center" width="10%">Cetak Sertifikat</th>
		</tr>
	</thead>
	<tbody>
	<?php foreach($data_siswa as $siswa){
	$link_cetak = ($siswa->nilai_ukk && $siswa->nilai_ukk->nilai) ? '<a class="btn btn-sm btn-success" href="'.url('cetak/sertifikat/'.$siswa->anggota_rombel_id.'/'.$siswa->nilai_ukk->rencana_ukk_id).'" target="_blank">Cetak</a>' : '';
	?>
		<tr>
			<td><?php echo ($siswa->siswa) ? strtoupper($siswa->siswa->nama) : '-'; ?></td>
			<td>
				<input type="hidden" class="form-control" name="peserta_didik_id[]" value="<?php echo $siswa->peserta_didik_id; ?>" />
				<input type="hidden" class="form-control" name="anggota_rombel_id[]" value="<?php echo $siswa->anggota_rombel_id; ?>" />
				<input type="number" class="form-control" name="nilai[]" value="{{($siswa->nilai_ukk) ? $siswa->nilai_ukk->nilai : ''}}" min="1" max="100" />
			</td>
			<td>{{($siswa->nilai_ukk) ? CustomHelper::keterangan_ukk($siswa->nilai_ukk->nilai) : ''}}</td>
			<td class="text-center"><?php echo $link_cetak; ?></td>
		</tr>
	<?php } ?>
	</tbody>
</table>