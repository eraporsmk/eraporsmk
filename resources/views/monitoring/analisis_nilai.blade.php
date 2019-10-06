@extends('adminlte::page')
@section('title_postfix', $title.' | ')
@section('content_header')
    <h1>{{$title}}</h1>
@stop

@section('content')
    <form action="{{ route('simpan_perencanaan') }}" method="post" class="form-horizontal" id="form">
		{{ csrf_field() }}
		<div class="col">
			<div class="col-sm-8">
				<div class="form-group">
					<label for="ajaran_id" class="col-sm-3 control-label">Tahun Ajaran</label>
					<div class="col-sm-9">
						<input type="hidden" name="guru_id" id="guru_id" value="{{$user->guru_id}}" />
						<input type="hidden" name="pembelajaran_id" id="pembelajaran_id" value="" />
						<input type="hidden" name="query" id="query" value="{{$query}}" />
						<input type="hidden" name="semester_id" id="semester_id" value="{{$semester->semester_id}}" />
						<input type="text" class="form-control" value="{{$semester->nama}} (SMT {{$semester->semester}})" readonly />
					</div>
				</div>
				<div class="form-group">
					<label for="kelas" class="col-sm-3 control-label">Tingkat Kelas</label>
					<div class="col-sm-9">
						<select name="kelas" class="select2 form-control" id="kelas">
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
						<select name="rombel_id" class="select2 form-control" id="rombel">
							<option value="">== Pilih Rombongan Belajar ==</option>
						</select>
					</div>
				</div>
				<div class="form-group">
					<label for="mapel" class="col-sm-3 control-label">Mata Pelajaran</label>
					<div class="col-sm-9">
						<select name="id_mapel" class="select2 form-control" id="mapel">
							<option value="">== Pilih Mata Pelajaran ==</option>
						</select>
					</div>
				</div>
				<div class="form-group">
					<label for="kompetensi" class="col-sm-3 control-label">Kompetensi Penilaian</label>
					<div class="col-sm-9">
						<select name="kompetensi_id" class="select2 form-control" id="kompetensi">
							<option value="">== Pilih Kompetensi Penilaian ==</option>
						</select>
					</div>
				</div>
				<div class="form-group">
					<label for="penilaian" class="col-sm-3 control-label">Rencana Penilaian</label>
					<div class="col-sm-9">
						<select name="rencana_penilaian" class="select2 form-control" id="penilaian">
							<option value="">== Pilih Rencana Penilaian ==</option>
						</select>
					</div>
				</div>
			</div>
		</div>
		<div style="clear:both;"></div>
		<div id="result"></div>
@stop
@section('box-footer')
		<div class="form-group" id="simpan" style="display:none;">
			<input class="btn btn-primary" type="submit" value="Proses">
		</div>
	</form>
@stop
@section('js')
<script>
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
$('.select2').select2();
$('#kelas').change(function(){
	$("#rombel").val('');
	$("#rombel").trigger('change.select2');
	$('#result').html('');
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
	$("#mapel").val('');
	$("#mapel").trigger('change.select2');
	$('#result').html('');
	var ini = $(this).val();
	if(ini == ''){
		return false;
	}
	$.ajax({
		url: '{{url('ajax/get-mapel')}}',
		type: 'post',
		data: $("form#form").serialize(),
		success: function(response){
			result = checkJSON(response);
			if(result == true){
				var data = $.parseJSON(response);
				$('#mapel').html('<option value="">== Pilih Mata Pelajaran ==</option>');
				if(!$.isEmptyObject(data.mapel)){
					$.each(data.mapel, function (i, item) {
						$('#mapel').append($("<option></option>")
						.attr("value",item.value)
						.attr("data-pembelajaran_id",item.pembelajaran_id)
						.text(item.text)); 
					});
				}
			} else {
				$('.simpan').show();
				$('#result').html(response);
			}
		}
	});
});
$('#mapel').change(function(){
	$("#kompetensi").val('');
	$("#kompetensi").trigger('change.select2');
	$('#result').html('');
	var ini = $(this).val();
	var selected = $(this).find('option:selected');
	var pembelajaran_id = selected.data('pembelajaran_id');
	$('#pembelajaran_id').val(pembelajaran_id);
	if(ini == ''){
		return false;
	}
	$.ajax({
		url: '{{url('/ajax/get-kompetensi')}}',
		type: 'post',
		data: $("form#form").serialize(),
		success: function(response){
			result = checkJSON(response);
			if(result == true){
				var data = $.parseJSON(response);
				$('#kompetensi').html('<option value="">== Pilih Kompetensi Penilaian ==</option>');
				if(!$.isEmptyObject(data.aspek_penilaian)){
					$.each(data.aspek_penilaian, function (i, item) {
						$('#kompetensi').append($("<option></option>")
						.attr("value",item.value)
						.text(item.text)); 
					});
				}
			} else {
				$('.simpan').show();
				$('#result').html(response);
			}
		}
	});
});
$('#kompetensi').change(function(){
	$("#penilaian").val('');
	$("#penilaian").trigger('change.select2');
	$('#result').html('');
	var ini = $(this).val();
	if(ini == ''){
		return false;
	}
	$.ajax({
		url: '{{url('/ajax/get-rencana')}}',
		type: 'post',
		data: $("form#form").serialize(),
		success: function(response){
			result = checkJSON(response);
			if(result == true){
				var data = $.parseJSON(response);
				$('#penilaian').html('<option value="">== Pilih Rencana Penilaian ==</option>');
				if(!$.isEmptyObject(data.result)){
					$.each(data.result, function (i, item) {
						$('#penilaian').append($("<option></option>")
						.attr("value",item.value)
						.text(item.text)); 
					});
				}
			} else {
				$('.simpan').show();
				$('#result').html(response);
			}
		}
	});
});
$('#penilaian').change(function(){
	$('#result').html('');
	var ini = $(this).val();
	if(ini == ''){
		return false;
	}
	$.ajax({
		url: '{{url('/ajax/get-analisis-nilai')}}',
		type: 'post',
		data: $("form#form").serialize(),
		success: function(response){
			$('#result').html(response);
		}
	});
});
</script>
@stop