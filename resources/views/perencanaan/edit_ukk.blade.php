@extends('adminlte::page')
@section('title_postfix', 'Edit Data Perencanaan Penilaian UKK |')
@section('content_header')
    <h1>Edit Data Perencanaan Penilaian UKK</h1>
@stop

@section('content')
    <form action="{{ route('perencanaan.update_ukk') }}" method="post" class="form-horizontal" id="form" accept-charset="UTF-8" enctype="multipart/form-data">
		{{ csrf_field() }}
		@if ($errors->any())
			<div class="alert alert-danger">
				@foreach ($errors->all() as $error)
					{{ $error }} <br>
				@endforeach
			</div>
		@endif
		<div class="row">
			<div class="col-sm-8">
				<div class="form-group">
					<label for="ajaran_id" class="col-sm-3 control-label">Tahun Ajaran</label>
					<div class="col-sm-9">
						<input type="hidden" name="rencana_ukk_id" value="{{$rencana_ukk->rencana_ukk_id}}" />
						<input type="text" class="form-control" value="{{$semester->nama}} (SMT {{$semester->semester}})" readonly />
					</div>
				</div>
				<div class="form-group">
					<label for="internal" class="col-sm-3 control-label">Penguji Internal</label>
					<div class="col-sm-9">
						<select name="internal" class="select2 form-control" id="internal" required>
							<option value="">== Pilih Penguji Internal ==</option>
							@foreach($internal as $intern)
							<option value="{{$intern->guru_id}}"{{($rencana_ukk->guru_internal->guru_id == $intern->guru_id) ? ' selected' : ''}}>{{CustomHelper::nama_guru($intern->gelar_depan, $intern->nama, $intern->gelar_belakang)}}</option>
							@endforeach
						</select>
					</div>
				</div>
				<div class="form-group">
					<label for="eksternal" class="col-sm-3 control-label">Penguji Eksternal</label>
					<div class="col-sm-9">
						<select name="eksternal" class="select2 form-control" id="eksternal" style="width:100%" required>
							<option value="">== Pilih Penguji Eksternal ==</option>
							@foreach($eksternal as $ekstern)
							<option value="{{$ekstern->guru_id}}"{{($rencana_ukk->guru_eksternal->guru_id == $ekstern->guru_id) ? ' selected' : ''}}>{{CustomHelper::nama_guru($ekstern->gelar_depan, $ekstern->nama, $ekstern->gelar_belakang)}} ({{$ekstern->dudi->nama}})</option>
							@endforeach
						</select>
					</div>
				</div>
				<div class="form-group">
					<label for="paket_ukk_id" class="col-sm-3 control-label">Paket Kompetensi</label>
					<div class="col-sm-9">
						<input type="text" class="form-control" value="{{$rencana_ukk->paket_ukk->nama_paket_id}}" readonly />
					</div>
				</div>
				<div class="form-group">
					<label for="tanggal_sertifikat" class="col-sm-3 control-label">Tanggal Sertifikat</label>
					<div class="col-sm-9">
						<input name="tanggal_sertifikat" value="{{date('d-m-Y', strtotime($rencana_ukk->tanggal_sertifikat))}}" class="form-control datepicker" id="tanggal_sertifikat" type="text" required />
					</div>
				</div>
			</div>
		</div>
		<div style="clear:both;"></div>
		<div class="row">
			<div class="col-sm-12">
				<div id="result"></div>
			</div>
		</div>
		<div class="row">
			<div class="col-sm-8">
				<input class="btn-simpan btn btn-primary" type="submit" value="Simpan">
			</div>
		</div>
	</form>
@stop
@section('js')
<script>
$('.datepicker').datepicker({
	autoclose: true,
	format: "dd-mm-yyyy"
});
$('.select2').select2();
var checkJSON = function(m) {
	if (typeof m == 'object') { 
		try{ m = JSON.stringify(m); 
		} catch(err) { 
			return false; 
		}
	}
	if (typeof m == 'string') {
		try{ m = JSON.parse(m); 
		} catch (err) {
			return false;
		}
	}
	if (typeof m != 'object') { 
		return false;
	}
	return true;
};
$('#kelas').change(function(){
	$("#rombel").val('');
	$("#rombel").trigger('change.select2');
	$("#internal").val('');
	$("#internal").trigger('change.select2');
	$("#eksternal").val('');
	$("#eksternal").trigger('change.select2');
	$("#paket_ukk_id").val('');
	$("#paket_ukk_id").trigger('change.select2');
	$("#tanggal_sertifikat").val('');
	$('.btn-simpan').hide();
	var ini = $(this).val();
	if(ini == ''){
		return false;
	}
	$.ajax({
		url: '{{url('ajax/get-rombel')}}',
		type: 'post',
		data: $("form#form").serialize(),
		success: function(response){
			result = checkJSON(response);
			if(result == true){
				$('.simpan').hide();
				$('#result').html('');
				$('table.table').addClass("jarak1");
				var data = $.parseJSON(response);
				$('#rombel').html('<option value="">== Pilih Rombongan Belajar ==</option>');
				if($.isEmptyObject(data.result)){
				} else {
					$.each(data.result, function (i, item) {
						$('#rombel').append($('<option>', { 
							value: item.value,
							text : item.text
						}));
					});
				}
			} else {
				$('#result').html(response);
			}
		}
	});
});
$('#rombel').change(function(){
	$("#jurusan_id").val('');
	var ini = $(this).val();
	if(ini == ''){
		return false;
	}
	$.ajax({
		url: '{{url('ajax/get-jurusan')}}',
		type: 'post',
		data: $("form#form").serialize(),
		success: function(response){
			$("#jurusan_id").val(response);
		}
	});
});
$('#eksternal').change(function(){
	$("#paket_ukk_id").val('');
	$("#paket_ukk_id").trigger('change.select2');
	var ini = $(this).val();
	if(ini == ''){
		return false;
	}
	$.ajax({
		url: '{{url('ajax/get-paket-by-jurusan')}}',
		type: 'post',
		data: $("form#form").serialize(),
		success: function(response){
			result = checkJSON(response);
			if(result == true){
				var data = $.parseJSON(response);
				$('#paket_ukk_id').html('<option value="">== Pilih Paket Kompetensi ==</option>');
				if($.isEmptyObject(data.result)){
				} else {
					$.each(data.result, function (i, item) {
						$('#paket_ukk_id').append($('<option>', { 
							value: item.value,
							text : item.text
						}));
					});
				}
			} else {
				$('#result').html(response);
			}
		}
	});
});
$('#paket_ukk_id').change(function(){
	var ini = $(this).val();
	if(ini == ''){
		return false;
	}
	$.ajax({
		url: '{{url('ajax/get-siswa-ukk')}}',
		type: 'post',
		data: $("form#form").serialize(),
		success: function(response){
			$('#result').html(response);
			$('.btn-simpan').show();
		}
	});
});
</script>
@stop