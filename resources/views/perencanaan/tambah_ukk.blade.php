@extends('adminlte::page')
@section('title_postfix', 'Tambah Data Perencanaan Penilaian UKK |')
@section('content_header')
    <h1>Tambah Data Perencanaan Penilaian UKK</h1>
@stop

@section('content')
    <form action="{{ route('perencanaan.simpan_ukk') }}" method="post" class="form-horizontal" id="form" accept-charset="UTF-8" enctype="multipart/form-data">
		{{ csrf_field() }}
		<div class="row">
			<div class="col-sm-8">
				<div class="form-group">
					<label for="ajaran_id" class="col-sm-3 control-label">Tahun Ajaran</label>
					<div class="col-sm-9">
						<input type="hidden" name="guru_id" value="{{$user->guru_id}}" />
						<input type="hidden" name="query" value="ukk" />
						<input type="hidden" name="sekolah_id" value="{{$user->sekolah_id}}" />
						<input type="hidden" name="semester_id" id="semester_id" value="{{$semester->semester_id}}" />
						<input type="text" class="form-control" value="{{$semester->nama}} (SMT {{$semester->semester}})" readonly />
					</div>
				</div>
				<div class="form-group">
					<label for="kelas" class="col-sm-3 control-label">Tingkat Kelas</label>
					<div class="col-sm-9">
						<select name="kelas" class="select2 form-control" id="kelas" required>
							<option value="">== Pilih Tingkat Kelas ==</option>
							<option value="10">Kelas 10</option>
							<option value="11">Kelas 11</option>
							<option value="12">Kelas 12</option>
							<option value="13">Kelas 13</option>
						</select>
					</div>
				</div>
				<div class="form-group">
					<label for="rombel" class="col-sm-3 control-label">Rombongan Belajar</label>
					<div class="col-sm-9">
						<select name="rombel_id" class="select2 form-control" id="rombel" required>
							<option value="">== Pilih Rombongan Belajar ==</option>
						</select>
						<input type="hidden" name="jurusan_id" id="jurusan_id" />
					</div>
				</div>
				<div class="form-group">
					<label for="internal" class="col-sm-3 control-label">Penguji Internal</label>
					<div class="col-sm-9">
						<select name="internal" class="select2 form-control" id="internal" required>
							<option value="">== Pilih Penguji Internal ==</option>
							@foreach($internal as $intern)
							<option value="{{$intern->guru_id}}">{{CustomHelper::nama_guru($intern->gelar_depan, $intern->nama, $intern->gelar_belakang)}}</option>
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
							<option value="{{$ekstern->guru_id}}">{{CustomHelper::nama_guru($ekstern->gelar_depan, $ekstern->nama, $ekstern->gelar_belakang)}} ({{$ekstern->dudi->nama}})</option>
							@endforeach
						</select>
					</div>
				</div>
				<div class="form-group">
					<label for="paket_ukk_id" class="col-sm-3 control-label">Paket Kompetensi</label>
					<div class="col-sm-9">
						<select name="paket_ukk_id" class="select2 form-control" id="paket_ukk_id" style="width:100%" required>
							<option value="">== Pilih Paket Kompetensi ==</option>
						</select>
					</div>
				</div>
				<div class="form-group">
					<label for="tanggal_sertifikat" class="col-sm-3 control-label">Tanggal Sertifikat</label>
					<div class="col-sm-9">
						<input name="tanggal_sertifikat" class="form-control datepicker" id="tanggal_sertifikat" type="text" required />
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
				<input class="btn btn-primary" type="submit" value="Simpan">
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
		}
	});
});
</script>
@stop