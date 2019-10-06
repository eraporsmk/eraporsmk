	<div class="form-group">
		<label for="mitra_prakrein" class="col-sm-2 control-label">Jenis Prestasi</label>
		<div class="col-sm-5">
			<select name="jenis_prestasi" id="jenis_prestasi" class="select2 form-control" required>
				<option value="">== Pilih Jenis Prestasi ==</option>
				<option value="Kurikuler">Kurikuler</option>
				<option value="Ekstra Kurikuler">Ekstra Kurikuler</option>
				<option value="Catatan Khusus Lainnya">Catatan Khusus Lainnya</option>
			</select>
		</div>
	</div>
	<div class="form-group">
		<label for="lokasi_prakerin" class="col-sm-2 control-label">Keterangan Prestasi</label>
		<div class="col-sm-5">
			<input type="text" name="keterangan_prestasi" id="keterangan_prestasi" class="form-control" required />			
		</div>
	</div>
	<div class="form-group">
		<div class="col-sm-5 col-sm-offset-2">
			<button type="submit" class="btn btn-success">Simpan</button>
		</div>
	</div>
	<div style="clear:both"></div>
	<div class="table-responsive no-padding">
		<table class="table table-bordered table-hover" style="margin-bottom:20px;">
			<thead>
				<tr>
					<th width="2%" style="vertical-align:middle;" class="text-center">No</th>
					<th width="30%" style="vertical-align:middle;">Jenis Prestasi</th>
					<th width="53%" style="vertical-align:middle;">Keterangan Prestasi</th>
					<th width="15%" style="vertical-align:middle;" class="text-center">Aksi</th>
				</tr>
			</thead>
			<tbody>
				@if($all_prestasi)
				{{--dd($all_prestasi->prestasi)--}}
				@foreach($all_prestasi->prestasi as $prestasi)
				<tr>
					<td class="text-center">{{$loop->iteration}}</td>
					<td>{{$prestasi->jenis_prestasi}}</td>
					<td>{{$prestasi->keterangan_prestasi}}</td>
					<td class="text-center">
						<a title="Ubah prestasi" href="{{url('laporan/edit-prestasi').'/'.$prestasi->prestasi_id}}" class="btn btn-warning btn-sm toggle-modal"><i class="fa fa-pencil"></i></a>
						<a title="Hapus prestasi" href="{{url('laporan/delete-prestasi').'/'.$prestasi->prestasi_id}}" class="btn btn-danger btn-sm confirm"><i class="fa fa-power-off"></i></a>
					</td>
				</tr>
				@endforeach
				@else
				<tr>
					<td colspan="4" class="text-center">Belum ada data untuk ditampilkan</td>
				</tr>
				@endif
			</tbody>
		</table>
	</div>
<script>
$('.select2').select2();
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