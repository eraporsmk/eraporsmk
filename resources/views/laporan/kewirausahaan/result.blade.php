	<div class="form-group">
		<label for="pola" class="col-sm-2 control-label">Pola Kewirausahaan</label>
		<div class="col-sm-5">
			<select name="pola" id="pola" class="select2 form-control" required>
				<option value="">== Pilih Pola Kewirausahaan ==</option>
				<option value="Individu">Individu</option>
				<option value="Kelompok">Kelompok</option>
			</select>
		</div>
	</div>
	<div id="anggota_wirausaha_show" class="form-group" style="display: none;">
		<label for="anggota_wirausaha" class="col-sm-2 control-label">Anggota Kewirausahaan</label>
		<div class="col-sm-5">
			<select name="anggota_wirausaha[]" id="anggota_wirausaha" class="select2 form-control" style="width: 100%" multiple>
				<option value="">== Pilih Anggota Kewirausahaan ==</option>
			</select>
		</div>
	</div>
	<div class="form-group">
		<label for="jenis" class="col-sm-2 control-label">Jenis Kewirausahaan</label>
		<div class="col-sm-5">
			<select name="jenis" id="jenis" class="select2 form-control" required>
				<option value="">== Pilih Jenis Kewirausahaan ==</option>
				<option value="Jasa">Jasa</option>
				<option value="Produk">Produk</option>
			</select>
		</div>
	</div>
	<div class="form-group">
		<label for="nama_produk" class="col-sm-2 control-label">Nama Produk Kewirausahaan</label>
		<div class="col-sm-5">
			<input type="text" name="nama_produk" id="nama_produk" class="form-control">
		</div>
	</div>
	<div class="form-group">
		<div class="col-sm-5 col-sm-offset-2">
			<button type="submit" class="btn btn-success">Simpan</button>
		</div>
	</div>
	<div style="clear:both"></div>
	<div class="table-responsive no-padding" style="display: none;">
		<table class="table table-bordered table-hover" style="margin-bottom:20px;">
			<thead>
				<tr>
					<th width="2%" style="vertical-align:middle;" class="text-center">No</th>
					<th width="20%" style="vertical-align:middle;">Pola Kewirausahaan</th>
					<th width="30%" style="vertical-align:middle;">Jenis Kewirausahaan</th>
					<th width="33%" style="vertical-align:middle;">Nama Produk Kewirausahaan</th>
					<th width="15%" style="vertical-align:middle;" class="text-center">Aksi</th>
				</tr>
			</thead>
			<tbody>
				@forelse ($kewirausahaan as $item)
				<tr>
					<td class="text-center">{{$loop->iteration}}</td>
					<td>{{$item->pola}}</td>
					<td>{{$item->jenis}}</td>
					<td>{{$item->nama_produk}}</td>
					<td class="text-center">
						<a title="Ubah Kewirausahaan" href="{{route('laporan.edit_kewirausahaan', ['id' => $item->kewirausahaan_id])}}" class="btn btn-warning btn-sm toggle-modal"><i class="fa fa-pencil"></i></a>
						<a title="Hapus Kewirausahaan" href="{{route('laporan.hapus_kewirausahaan', ['id' => $item->kewirausahaan_id])}}" class="btn btn-danger btn-sm confirm"><i class="fa fa-power-off"></i></a>
					</td>
				</tr>	
				@empty
				<tr>
					<td colspan="5" class="text-center">Belum ada data untuk ditampilkan</td>
				</tr>
				@endforelse
			</tbody>
		</table>
	</div>
<script>
$('.select2').select2();
$('#pola').change(function(){
	var ini = $(this).val();
	if(ini == 'Kelompok'){
		$.ajax({
			url: '{{route('ajax.get_anggota_wirausaha')}}',
			type: 'post',
			data: $('#form').serialize(),
			success: function(response){
				$('#anggota_wirausaha_show').show();
				$("#anggota_wirausaha").html('<option value="">== Pilih Anggota Kewirausahaan ==</option>');
				if(!$.isEmptyObject(response.results)){
					$.each(response.results, function (i, item) {
						$('#anggota_wirausaha').append($('<option>', { 
							value: item.value,
							text : item.text
						}));
					});
				}
			}
		});
	}
});
$('a.toggle-modal').bind('click',function(e) {
	e.preventDefault();
	var url = $(this).attr('href');
	if (url.indexOf('#') == 0) {
		$('#modal_content').modal('open');
	       $('.editor').wysihtml5();
	} else {
		$.get(url, function(data) {
			$('#modal_content').modal();
			$('#modal_content').html(data);
		});
	}
});
$('a.confirm').bind('click',function(e) {
	var ini = $(this).parents('tr');
	e.preventDefault();
	var url = $(this).attr('href');
	swal({
		title: "Anda Yakin?",
		text: "Tindakan ini tidak bisa dikembalikan!",
		icon: "warning",
		buttons: true,
		dangerMode: true,
		closeOnClickOutside: false,
	}).then((willDelete) => {
		if (willDelete) {
			$.get(url).done(function(response) {
				var data = $.parseJSON(response);
				swal({title: data.title, text: data.text,icon: data.icon, closeOnClickOutside: false}).then(function(){
					$.ajax({
						url: '{{url('ajax/get-prestasi')}}',
						type: 'post',
						data: $('#form').serialize(),
						success: function(response){
							$('.simpan').show();
							$('#result').html(response);
						}
					});
				});
			});
		}
	});
});
</script>