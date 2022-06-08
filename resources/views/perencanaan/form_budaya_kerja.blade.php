<div class="table-responsive">
	<table class="table table-striped table-bordered" id="clone">
	<thead>
		<tr>
			<th class="text-center" style="min-width:110px; vertical-align:middle;" rowspan="2">Nama Projek</th>
			<th class="text-center" style="min-width:110px; vertical-align:middle;" rowspan="2">Deskripsi</th>
			<th class="text-center" colspan="{{$budaya_kerja->count()}}">Aspek Dinilai</th>
		</tr>
		<tr>
			@foreach ($budaya_kerja as $item)
			<td>{{$item->aspek}}</td>
			@endforeach
		</tr>
	</thead>
	<tbody>
		<?php for ($i = 1; $i <= 5; $i++) {?>
		<tr>
			<td>
				<input class="form-control input-sm" type="text" name="nama_projek[]" value="">
			</td>
			<td>
				<textarea name="deskripsi[]" rows="3" class="form-control"></textarea>
			</td>
			@foreach ($budaya_kerja as $item)
			<td style="vertical-align:middle;">
				<div class="text-center"><input type="checkbox" name="aspek_<?php echo ($i - 1); ?>[]" value="<?php echo $item->budaya_kerja_id; ?>" /></div>
			</td>
			@endforeach
		</tr>
		<?php } ?>
	</tbody>
</table>
</div>
	<a class="clone btn btn-danger pull-left">Tambah Aktivitas Penilaian</a>
	<button type="submit" class="btn btn-success pull-right">Simpan</button>
<script>
$('button.simpan').remove();
var i = <?php echo isset($i) ? $i : 0; ?>;
$("a.clone").click(function() {
	$("table#clone tbody tr:last").clone().find("td").each(function() {
		$(this).find('input[type=hidden]').attr('name', 'kd_id_'+i+'[]');
		$(this).find('input[type=checkbox]').attr('name', 'kd_'+i+'[]');
	}).end().appendTo("table#clone");
	i++;
});
</script>