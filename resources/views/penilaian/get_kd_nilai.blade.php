<?php
if($rombongan_belajar->kunci_nilai){
?>
<script>
$('#simpan').remove();
</script>
<div class="alert alert-danger alert-dismissible">
	<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
	<h4><i class="icon fa fa-info"></i> Informasi!</h4>
	Status penilaian tidak aktif. Untuk menambah atau memperbaharui penilaian, silahkan menghubungi wali kelas.
</div>
<?php
} else {
?>
@if(isset($all_kd_nilai->kd_nilai) && $all_kd_nilai->kd_nilai->count())
<link rel="stylesheet" href="{{ asset('vendor/adminlte/plugins/tooltip/tooltip-viewport.css') }}">
<script src="{{ asset('vendor/adminlte/plugins/tooltip/tooltip-viewport.js') }}"></script>
<script src="{{ asset('vendor/blueimp/jquery-file-upload/js/vendor/jquery.ui.widget.js') }}"></script>
<script src="{{ asset('vendor/blueimp/jquery-file-upload/js/jquery.iframe-transport.js') }}"></script>
<script src="{{ asset('vendor/blueimp/jquery-file-upload/js/jquery.fileupload.js') }}"></script>
{{--dd($all_kd_nilai)--}}
<input type="hidden" name="bobot" value="{{$bobot}}" />
<input type="hidden" name="all_bobot" value="{{$all_bobot}}" />
<input type="hidden" name="jumlah_kd" value="{{$all_kd_nilai->kd_nilai->count()}}" />
<div class="row">
	<div class="col-md-6">
		<a class="btn btn-success btn-lg btn-block" href="<?php echo url('penilaian/exportToExcel/'.$rencana_penilaian_id); ?>">Unduh Format Excel</a>
	</div>
	<div class="col-md-6">
		<p class="text-center"><span class="btn btn-danger btn-file btn-lg btn-block"> Unggah Format Excel <input type="file" id="fileupload" name="file" /></span></p>
	</div>
</div>
<table class="table table-bordered table-hover">
	<thead>
		<tr>
			<th rowspan="2" style="vertical-align: middle;">Nama Peserta Didik</th>
			<th class="text-center" colspan="{{$all_kd_nilai->kd_nilai->count()}}">Kompetensi Dasar</th>
			<?php if($kompetensi_id == 1){ ?>
			<th rowspan="2" style="vertical-align: middle;" class="text-center">Rerata Nilai</th>
			<?php } ?>
		</tr>
		<?php
		//dd($all_kd_nilai->kd_nilai);
		foreach($all_kd_nilai->kd_nilai as $kd_nilai){
			$kompetensi_dasar = ($kd_nilai->kompetensi_dasar->kompetensi_dasar_alias) ? $kd_nilai->kompetensi_dasar->kompetensi_dasar_alias : $kd_nilai->kompetensi_dasar->kompetensi_dasar;
		?>
				<th class="text-center"><a href="javacript:void(0)" class="tooltip-left" title="<?php echo $kompetensi_dasar; ?>"><?php echo $kd_nilai->id_kompetensi; ?></a></th>
		<?php } ?>
	</thead>
	<tbody>
	<?php
	$no=0;
	foreach($all_anggota as $anggota){
		$siswa = $anggota->siswa;
	?>
	<input class="set_nilai" type="hidden" name="siswa_id[]" value="<?php echo $anggota->anggota_rombel_id; ?>" />
	<tr>
		<td><?php echo strtoupper($siswa->nama); ?></td>
		<?php
		foreach($all_kd_nilai->kd_nilai as $kd_nilai){
			$nilai = App\Nilai::where('kd_nilai_id', '=', $kd_nilai->kd_nilai_id)->where('anggota_rombel_id', '=', $anggota->anggota_rombel_id)->first();
			//where('anggota_rombel_id', '=', $anggota->anggota_rombel_id)->first();
			$nilai_value 	= ($nilai) ? $nilai->nilai : '';
			$rerata 		= ($nilai) ? $nilai->rerata : '';
			$rerata_jadi 	= ($nilai) ? $nilai->rerata_jadi : '';
		?>
		<td><input type="text" name="kd[<?php echo $kd_nilai->kd_nilai_id; ?>][]" size="10" class="form-control" value="<?php echo $nilai_value; ?>" autocomplete="off" maxlength="3" /></td>
		<?php } 
		if($kompetensi_id == 1){ ?>
			<td><input type="text" name="rerata[]" id="rerata_<?php echo $no; ?>" size="10" class="form-control" value="<?php echo $rerata; ?>" readonly /></td>
			<input type="hidden" name="rerata_jadi[]" id="rerata_jadi_<?php echo $no; ?>" size="10" class="form-control" value="<?php echo $rerata_jadi; ?>" />
		<?php } else { ?>
		<input type="hidden" name="rerata[]" id="rerata_<?php echo $no; ?>" size="10" class="form-control" value="<?php echo $rerata; ?>" />
		<input type="hidden" name="rerata_jadi[]" id="rerata_jadi_<?php echo $no; ?>" size="10" class="form-control" value="<?php echo $rerata_jadi; ?>" />
		<?php } ?>
	</tr>
	<?php $no++;
	} ?>
	</tbody>
</table>
<script>
$(function() {
	$.ajaxSetup({
		headers: {
			'X-CSRF-TOKEN': '{{ csrf_token() }}'
		}
	});
	$('#fileupload').fileupload({
		url: '{{url('/penilaian/import_excel')}}',
		dataType: 'json',
		progressall: function(e, data) {
			var progress = parseInt(data.loaded / data.total * 100, 10);
			$('#progress .progress-bar').css('width', progress + '%');
		},
		done: function(e, data) {
			//console.log(data.result.sheetData[0]);
			if(typeof data.result.title == 'undefined'){
				if(data.result.sheetData[0][0].rerata == 'ERROR') {
					swal({
						title: 'ERROR', 
						text: 'Ada nilai dengan format tidak sesuai. Silakan perbaiki lalu unggah kembali !',
						icon: "error",
						dangerMode: "true", 
						closeOnClickOutside: true
					});
				} else {
					var cari_form = $('body').find('.set_nilai');
					$(cari_form).each(function(i,v) {
						var cari_input = $(this).next().find('input[type=text]');
						$(cari_input).each(function(a,b) {
							//console.log(data.result.sheetData[0][i].nilai[a]);
							$(this).val(data.result.sheetData[0][i].nilai[a]);
						});
					});
				}
			}
			$.each(data.result.files, function(index, file) {
				console.log('ini');
				console.log(file);
			});
			$('#progress').css('width', '0%');
		},
		error: function(data) {
			$.each(data.responseJSON.errors.file, function(index, message) {
				console.log(message);
			});
			$('#progress .progress-bar').css('width','0%');
		}
	});
});
</script>
@else
@endif
<?php } ?>
