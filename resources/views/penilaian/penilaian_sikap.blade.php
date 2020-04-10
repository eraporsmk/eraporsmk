<div class="col-sm-12" id="form_sikap" style="display:none;">
	<div class="table-responsive">
		<table class="table table-bordered">
			<thead>
			@foreach($all_sikap as $sikap)
				<th width="20%" class="text-center">{{$sikap->butir_sikap}}</th>
			@endforeach
			</thead>
			<tbody>
				<tr>
				@foreach($all_sikap as $sikap)
					<td>
					<ul style="padding-left:10px;">
					@foreach($sikap->sikap as $subsikap)
					<li>{{$subsikap->butir_sikap}}</li>
					@endforeach
					</ul>
					</td>
				@endforeach
				</tr>
			</tbody>
		</table>
	</div>
	<div class="form-group">
		<label for="tanggal_sikap" class="col-sm-2 control-label">Tanggal</label>
		<div class="input-group col-sm-2">
			<input type="text" name="tanggal_sikap" id="tanggal_sikap" class="form-control datepicker" data-date-format="dd-mm-yyyy" required />
			<span class="input-group-addon"><i class="fa fa-calendar"></i></span>
		</div>
	</div>
	<div style="margin-left:-20px;">
		<div class="form-group">
			<label for="butir_sikap" class="col-sm-2 control-label">Butir Sikap</label>
			<div class="col-sm-3">
				<select name="sikap_id" class="form-control" id="sikap_id" required>
					<option value="">== Pilih Butir Sikap ==</option>
					@foreach($all_sikap as $ref_sikap)
					<option value="{{$ref_sikap->sikap_id}}">{{$ref_sikap->butir_sikap}}</option>
					@endforeach
				</select>
			</div>
			<div class="col-sm-3">
				<select name="opsi_sikap" class="form-control" id="opsi_sikap" required>
					<option value="1">Positif</option>
					<option value="2">Negatif</option>
				</select>
			</div>
		</div>
	</div>
	<div class="form-group" style="margin-top:20px;">
		<label for="uraian_sikap" class="col-sm-2 control-label">Catatan Perilaku</label>
		<div class="input-group col-sm-8">
			<input type="text" class="form-control" name="uraian_sikap" id="uraian_sikap" required />
		</div>
	</div>
	<div class="form-group">
		<div class="row">
			<div class="col-sm-5 col-md-offset-2">
				<button type="submit" class="btn btn-success simpan">Simpan</button>
				<a class="btn btn-danger cancel" href="javascript:void(0);">Batal</a>
			</div>
		</div>
	</div>
</div>
<a class="btn btn-success btn-block btn-lg add" href="javascript:void(0);">Tambah Data</a>
<table class="table table-bordered table-hover" style="margin-top:20px;">
	<thead>
		<th>No</th>
		<th>Tanggal</th>
		<th>Butir Sikap</th>
		<th>Predikat</th>
		<th>Catatan Perilaku</th>
		<th class="text-center">Tindakan</th>
	</thead>
	<tbody>
	@if($nilai_sikap->count())
		@foreach($nilai_sikap as $sikap)
		<tr>
			<td>{{ $loop->iteration }}</td>
			<td>{{date('d/m/Y',strtotime($sikap->tanggal_sikap))}}</td>
			<td>{{$sikap->ref_sikap->butir_sikap}}</td>
			<td>{{($sikap->opsi_sikap == 1) ? 'Positif' : 'Negatif'}}</td>
			<td>{{$sikap->uraian_sikap}}</td>
			<td>
				@if($guru_id == $sikap->guru_id)
				<div class="text-center">
					<div class="btn-group">
						<button type="button" class="btn btn-default btn-sm">Aksi</button>
						<button type="button" class="btn btn-info btn-sm dropdown-toggle" data-toggle="dropdown">
							<span class="caret"></span>
							<span class="sr-only">Toggle Dropdown</span>
						</button>
						<ul class="dropdown-menu pull-right text-left" role="menu">
							<li><a href="{{url('penilaian/edit-sikap/'.$sikap->nilai_sikap_id)}}" class="toggle-modal"><i class="fa fa-pencil"></i> Ubah</a></li>
							<li><a href="{{url('penilaian/delete-sikap/'.$sikap->nilai_sikap_id)}}" class="confirm"><i class="fa fa-power-off"></i> Hapus</a></li>
						</ul>
					</div>
				</div>
				@else
					-
				@endif
			</td>
		</tr>
		@endforeach
		@else
		<tr>
			<td colspan="6" class="text-center">Tidak ada data untuk ditampilkan</td>
		</tr>
		@endif
	</tbody>
</table>
<script>
$('.datepicker').datepicker({
	autoclose: true,
	format: "dd-mm-yyyy",
});
$('.add').click(function(){
	$('#form_sikap').fadeIn();
	$('.add').fadeOut();
});
$('.cancel').click(function(){
	$('#form_sikap').fadeOut();
	$('.add').fadeIn();
});
$('a.toggle-modal').bind('click',function(e) {
	e.preventDefault();
	var url = $(this).attr('href');
	if (url.indexOf('#') == 0) {
		$('#modal_content').modal('open');
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
		title: "Apakah Anda yakin?",
		text: "Tindakan ini tidak dapat dikembalikan!",
		icon: "warning",
		buttons: true,
		dangerMode: true,
		closeOnClickOutside: false,
	}).then((willDelete) => {
		if (willDelete) {
		$.get(url).done(function(response) {
				var data = $.parseJSON(response);
				swal(data.title, {icon: data.icon,}).then((result) => {
					ini.remove();
				});
			});
		}
	});
});
</script>