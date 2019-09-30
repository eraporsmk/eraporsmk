@extends('adminlte::page')
@section('title_postfix', 'Tambah Referensi Paket Kompetensi |')
@section('content_header')
    <h1>Tambah Referensi Paket Kompetensi</h1>
@stop

@section('content')
	@if ($message = Session::get('success'))
      <div class="alert alert-success alert-block alert-dismissable"><i class="fa fa-check"></i>
        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
          <strong>Sukses!</strong> {{ $message }}
      </div>
    @endif

    @if ($message = Session::get('error'))
      <div class="alert alert-danger alert-block alert-dismissable"><i class="fa fa-ban"></i>
        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
        <strong>Error!</strong> {{ $message }}
      </div>
    @endif
	<form action="{{ route('referensi.simpan_ukk') }}" method="post" class="form-horizontal" id="form">
		{{ csrf_field() }}
		<input type="hidden" name="query" value="paket_ukk" />
		<div class="form-group">
			<label for="jurusan" class="col-sm-2 control-label">Kompetensi Keahlian</label>
			<div class="col-sm-5">
				<select name="jurusan_id" class="select2 form-control" id="jurusan_id" required>
					<option value="">== Pilih Kompentensi Keahlian ==</option>
					@if($all_jurusan->count())
					@foreach($all_jurusan as $jurusan)
					<option value="{{$jurusan->jurusan_id}}">{{$jurusan->nama_jurusan_sp}}</option>
					@endforeach
					@endif
				</select>
			</div>
		</div>
		<div class="form-group">
			<label for="kurikulum_id" class="col-sm-2 control-label">Kurikulum</label>
			<div class="col-sm-5">
				<input type="hidden" class="form-control" id="kode_kompetensi_val" name="kode_kompetensi" />
				<select name="kurikulum_id" class="select2 form-control" id="kurikulum_id" required>
					<option value="">== Pilih Kurikulum ==</option>
				</select>
			</div>
		</div>
		<!--div class="form-group" id="kode_kompetensi" style="display:none;">
			<label for="kode_kompetensi" class="col-sm-2 control-label">Kode Kompetensi</label>
			<div class="col-sm-5">
				<input type="text" class="form-control" id="kode_kompetensi_select" disabled="" />
			</div>
		</div-->
		<table class="table table-striped table-bordered" id="clone">
			<thead>
				<tr>
					<th width="10%">Nomor Paket</th>
					<th width="40%">Judul Paket (ID)</th>
					<th width="40%">Judul Paket (EN)</th>
					<th width="10%">Status</th>
				</tr>
			</thead>
			<tbody>
			<?php for ($i = 1; $i <= 5; $i++) {?>
				<tr>
					<td><input type="text" class="form-control" name="nomor_paket[]" /></td>
					<td><input type="text" class="form-control" name="nama_paket_id[]" /></td>
					<td><input type="text" class="form-control" name="nama_paket_en[]" /></td>
					<td>
						<select name="status[]" class="form-control" id="status" required>
							<option value="1">Aktif</option>
							<option value="0">Tidak Aktif</option>
						</select>
					</td>
				</tr>
			<?php } ?>
			</tbody>
		</table>
		<a class="clone btn btn-danger pull-left">Tambah Form Paket</a>
		<button type="submit" class="btn btn-success pull-right"><?php echo isset($data) ? 'Update' : 'Simpan'; ?></button>
	</form>
@stop
@section('js')
<script type="text/javascript">
$('.select2').select2();
$('#jurusan_id').change(function(){
	var ini = $(this).val();
	if(ini == ''){
		return false;
	}
	$.ajax({
		url: '{{url('ajax/get-kurikulum')}}',
		type: 'post',
		data: $("form").serialize(),
		success: function(response){
			var data = $.parseJSON(response);
			console.log(data);
			$('#kurikulum_id').html('<option value="">== Pilih Kurikulum ==</option>');
			if($.isEmptyObject(data.result)){
			} else {
				$.each(data.result, function (i, item) {
					$('#kurikulum_id').append($('<option>', { 
						value: item.value,
						text : item.text
					}));
				});
			}
		}
	});
});
$('#kurikulum_id').change(function(){
	$('.prepend').remove();
	var ini = $(this).val();
	if(ini == ''){
		$('#kode_kompetensi').hide();
		return false;
	}
	$('#kode_kompetensi').show();
	$('#kode_kompetensi_select').val(ini);
	$('#kode_kompetensi_val').val(ini);
	$.ajax({
		url: '{{url('ajax/get-paket-tersimpan')}}',
		type: 'post',
		data: $("form").serialize(),
		success: function(response){
			$("table#clone tbody").prepend(response);
			console.log(response);
		}
	});
});
var i = <?php echo isset($i) ? $i : 0; ?>;
$("a.clone").click(function() {
	$("table#clone tbody tr:last").clone().find("td").each(function() {
		$(this).find('input[type=text]').val('');
	}).end().appendTo("table#clone");
	i++;
});
</script>
@stop