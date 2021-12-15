<?php
if($rombongan_belajar->kunci_nilai){
?>
<div class="alert alert-danger alert-dismissible">
	<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
	<h4><i class="icon fa fa-info"></i> Informasi!</h4>
	Status penilaian tidak aktif. Untuk menambah perencanaan, silahkan menghubungi wali kelas.
</div>
<?php
} else {
?>
<link rel="stylesheet" href="{{ asset('vendor/adminlte/plugins/tooltip/tooltip-viewport.css') }}">
<script src="{{ asset('vendor/adminlte/plugins/tooltip/tooltip-viewport.js') }}"></script>
@if($all_kd->count())
<?php
foreach($all_kd as $kompetensi_dasar){
	$data_kd[str_replace('.','',$kompetensi_dasar->id_kompetensi)] = $kompetensi_dasar;
}
ksort($data_kd);
?>
<input type="hidden" name="bobot_value" id="bobot_value" value="{{$bobot}}" />
<div class="table-responsive">
	<table class="table table-striped table-bordered" id="clone">
	<thead>
		<tr>
			<th class="text-center" style="min-width:110px">Aktifitas Penilaian</th>
			<th class="text-center" style="min-width:110px;">Jenis Penilaian</th>
			<th class="text-center" width="10">Bobot</th>
			<?php
			foreach($data_kd as $kd){
				$kompetensi_dasar = ($kd->kompetensi_dasar_alias) ? $kd->kompetensi_dasar_alias : $kd->kompetensi_dasar;
			?>
			<th class="text-center"><a href="javascript:void(0)" class="tooltip-top" title="<?php echo strip_tags($kompetensi_dasar); ?>"><?php echo $kd->id_kompetensi; ?></a></th>
			<?php
			} 
			?>
			<th class="text-center">Keterangan</th>
		</tr>
	</thead>
	<tbody>
		<?php for ($i = 1; $i <= 5; $i++) {?>
		<tr>
			<td>
				<input class="form-control input-sm" type="text" name="nama_penilaian[]" value="" placeholder="<?php echo $placeholder; ?>">
			</td>
			<td>
				<select id="pilih_bobot" class="form-control input-sm" name="bentuk_penilaian[]">
					<option value="">- Pilih -</option>
					<?php 
					if($bentuk_penilaian){
						foreach($bentuk_penilaian as $value){ ?>
					<option value="<?php echo $value->teknik_penilaian_id; ?>" data-bobot="<?php echo $value->bobot; ?>"><?php echo $value->nama; ?></option>
					<?php } 
					} else {
					?>
					<option value="">Belum ada</option>
					<?php } ?>
				</select>
			</td>
			<td>
				<input class="form-control input-sm bobot" type="text" name="bobot_penilaian[]" value="">
			</td>
			<?php
			foreach($data_kd as $kd){
			?>
			<td style="vertical-align:middle;">
				<input type="hidden" name="kd_id_<?php echo $i; ?>[]" value="<?php echo $kd->kompetensi_dasar_id; ?>" />
				<div class="text-center"><input type="checkbox" name="kd_<?php echo $i; ?>[]" value="<?php echo $kd->id_kompetensi; ?>|<?php echo $kd->kompetensi_dasar_id; ?>" /></div>
			</td>
			<?php } ?>
			<td><input class="form-control input-sm" type="text" name="keterangan_penilaian[]" value=""></td>
		</tr>
		<?php } ?>
	</tbody>
</table>
</div>
	<a class="clone btn btn-danger pull-left">Tambah Aktivitas Penilaian</a>
	<button type="submit" class="btn btn-success pull-right">Simpan</button>
	@else
		<h3 class="text-center">Kompetensi Dasar belum tersedia <br />
		<a class="btn btn-sm btn-success" href="<?php echo url('/referensi/add-kd/'.$kompetensi_id.'/'.$id_rombel.'/'.$id_mapel.'/'.$kelas); ?>" target="_blank">Tambah Data Kompetensi Dasar</a></h3>
	@endif
<script>
$('a.generate_rencana').attr('href', '<?php echo url('admin/get_excel/perencanaan/'.$kompetensi_id.'/'.$id_rombel.'/'.$id_mapel); ?>')
$('button.simpan').remove();
var i = <?php echo isset($i) ? $i : 0; ?>;
$("a.clone").click(function() {
	$("table#clone tbody tr:last").clone().find("td").each(function() {
		$(this).find('input[type=hidden]').attr('name', 'kd_id_'+i+'[]');
		$(this).find('input[type=checkbox]').attr('name', 'kd_'+i+'[]');
	}).end().appendTo("table#clone");
	i++;
});
var bobot = $('#bobot');
var bobot_value = $('#bobot_value').val();
$('#bobot').val(bobot_value);
$('select#pilih_bobot').change(function(e) {
	e.preventDefault();
	var ini = $(this).val();
	var get_bobot = $(this).find("option:selected").data('bobot');
	if(ini == ''){
		$(this).closest('td').next('td').find('input').val('');
	} else {
		$(this).closest('td').next('td').find('input').val(get_bobot);
	}
});
<?php if($bobot){?>
	$("input#bobot").prop('disabled', true);
	<?php } else { ?>
	$("input#bobot").prop('disabled', false);
	<?php } ?>
<?php } ?>