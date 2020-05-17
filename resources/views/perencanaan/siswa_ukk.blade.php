<table class="table table-bordered table-striped table-hover">
	<thead>
		<tr>
			<th width="5%" class="text-center"><input id="checkAll" type="checkbox"></th>
			<th width="50%">Nama Peserta Didik</th>
			<th width="50%" class="text-center">Paket</th>
		</tr>
	</thead>
	<tbody>
	<input type="hidden" name="rombongan_belajar_id" value="{{$rombongan_belajar_id}}" />
	<?php $i=0;?>
	@foreach($data_siswa as $siswa)
		<input type="hidden" class="form-control" name="peserta_didik_id[]" value="{{$siswa->siswa->peserta_didik_id}}" />
		<input type="hidden" class="form-control" name="anggota_rombel_id[]" value="{{$siswa->anggota_rombel_id}}" />
		<tr>
			<td class="text-center">
				@if($siswa->nilai_ukk && $rencana_ukk)
				<input type="checkbox" checked="checked" disabled="disabled" />
				@else
				<input name="siswa_dipilih[<?php echo $i++;?>]" type="checkbox" value="1">
				@endif
			</td>
			<td>{{strtoupper($siswa->siswa->nama)}}</td>
			<td>{{($siswa->nilai_ukk && $rencana_ukk) ? $rencana_ukk->paket_ukk->nama_paket_id : '-'}}</td>
		</tr>
		{{--dd($siswa)--}}
	@endforeach
	{{--dd($rencana_ukk)--}}
	<?php
	/*$i=0;
	foreach($data_siswa as $siswa){
		$penilaian_ukk = 0;
		if($rencana_ukk){
			$penilaian_ukk = $this->penilaian_ukk->find("rencana_ukk_id = '$rencana_ukk->rencana_ukk_id' AND anggota_rombel_id = '$siswa->anggota_rombel_id'");
		}
	?>
		<input type="hidden" class="form-control" name="siswa_id[]" value="<?php echo $siswa->siswa_id; ?>" />
		<input type="hidden" class="form-control" name="anggota_rombel_id[]" value="<?php echo $siswa->anggota_rombel_id; ?>" />
		<tr>
			<td class="text-center"><?php if(!$penilaian_ukk){?><input name="siswa_dipilih[<?php echo $i ?>]" type="checkbox" value="1"><?php } ?></td>
			<td><?php echo ($siswa->siswa) ? strtoupper($siswa->siswa->nama) : '-'; ?></td>
			<td><?php echo ($penilaian_ukk) ? get_nama_paket($paket_ukk_id) : '-'; ?></td>
		</tr>
	<?php $i++;} */?>
	</tbody>
</table>
<script>
$("#checkAll").click(function () {
     $('input:checkbox').not(this).prop('checked', this.checked);
 });
$('input:checkbox').change(function() {
	if ($(this).is(":checked")) {
		$('#pilih_paket').show();
	} else {
		$('#pilih_paket').hide();
	}
});
@if($rencana_ukk)
$('#nomor_sertifikat').val('{{$rencana_ukk->nomor_sertifikat}}');
$('#tanggal_sertifikat').val('{{$rencana_ukk->tanggal_sertifikat}}');
$('#nomor_sertifikat').prop('readonly', true);
$('#tanggal_sertifikat').prop('readonly', true);
@endif
</script>