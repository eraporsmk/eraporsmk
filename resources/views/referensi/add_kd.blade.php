@extends('adminlte::page')
@section('title_postfix', 'Tambah Data Kompetensi Dasar |')
@section('content_header')
    <h1>Tambah Data Kompetensi Dasar</h1>
@stop

@section('content')
    <form action="{{ route('referensi.simpan_kd') }}" method="post" class="form-horizontal" id="form">
		{{ csrf_field() }}
				<div class="form-group">
					<label for="ajaran_id" class="col-sm-2 control-label">Tahun Ajaran</label>
					<div class="col-sm-5">
						<input type="hidden" name="semester_id" value="{{$semester->semester_id}}" />
						<input type="text" class="form-control" value="{{$semester->nama}} (SMT {{$semester->semester}})" readonly />
					</div>
				</div>
				<div class="form-group">
					<label for="kelas" class="col-sm-2 control-label">Tingkat Kelas</label>
					<div class="col-sm-5">
						<select name="kelas" class="select2 form-control" id="kelas">
							<option value="">== Pilih Tingkat Kelas ==</option>
							<option value="10"{{($kelas) ? ($kelas == 10) ? ' selected="selected"' : '': ''}}>Kelas 10</option>
							<option value="11"{{($kelas) ? ($kelas == 11) ? ' selected="selected"' : '': ''}}>Kelas 11</option>
							<option value="12"{{($kelas) ? ($kelas == 12) ? ' selected="selected"' : '': ''}}>Kelas 12</option>
							<option value="13"{{($kelas) ? ($kelas == 13) ? ' selected="selected"' : '': ''}}>Kelas 13</option>
						</select>
					</div>
				</div>
				<div class="form-group">
					<label for="rombel" class="col-sm-2 control-label">Rombongan Belajar</label>
					<div class="col-sm-5">
						<select name="rombel_id" class="select2 form-control" id="rombel">
							<option value="">== Pilih Rombongan Belajar ==</option>
							<option value="{{($rombongan_belajar) ? $rombongan_belajar->rombongan_belajar_id : ''}}" selected="selected">{{($rombongan_belajar) ? $rombongan_belajar->nama : ''}}</option>
						</select>
					</div>
				</div>
				<div class="form-group">
					<label for="mapel" class="col-sm-2 control-label">Mata Pelajaran</label>
					<div class="col-sm-5">
						<select name="id_mapel" class="select2 form-control" id="mapel">
							<option value="">== Pilih Mata Pelajaran ==</option>
							<option value="{{($pembelajaran) ? $pembelajaran->mata_pelajaran_id : ''}}" selected="selected">{{($pembelajaran) ? $pembelajaran->nama_mata_pelajaran : ''}}</option>
						</select>
					</div>
				</div>
				<div class="form-group">
					<label for="teknik_penilaian" class="col-sm-2 control-label">Aspek Penilaian</label>
					<div class="col-sm-5">
						<select name="kompetensi_id" class="select2 form-control" id="kompetensi_id" style="width:100%">
							<option value="1"{{($kompetensi_id) ? ($kompetensi_id == 1) ? ' selected="selected"' : '': ''}}>Pengetahuan</option>
							<option value="2"{{($kompetensi_id) ? ($kompetensi_id == 2) ? ' selected="selected"' : '': ''}}>Keterampilan</option>
							<option value="3"{{($kompetensi_id) ? ($kompetensi_id == 3) ? ' selected="selected"' : '': ''}}>Pusat Keunggulan</option>
						</select>
					</div>
				</div>
				<?php
				if($kompetensi_id == 3){
					$kode = 'Elemen';
					$isi = 'Deskripsi';
				} else {
					$kode = 'Kode KD';
					$isi = 'Isi KD';
				}
				?>
				<div class="form-group">
					<label for="id_kompetensi" class="col-sm-2 control-label">{{$kode}}</label>
					<div class="col-sm-5">
						<input type="text" name="id_kompetensi" id="id_kompetensi" class="form-control" />
					</div>
				</div>
				<div class="form-group">
					<label for="kompetensi_dasar" class="col-sm-2 control-label">{{$isi}}</label>
					<div class="col-sm-7">
						<textarea rows="5" name="kompetensi_dasar" id="kompetensi_dasar" class="form-control"></textarea>
					</div>
				</div>
				<div class="form-group">
					<div class="col-sm-7 col-sm-offset-2">
						<input class="btn btn-primary" type="submit" value="Simpan">
					</div>
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
	var ini = $(this).val();
	var selected = $(this).find('option:selected');
	var pembelajaran_id = selected.data('pembelajaran_id');
	$('#pembelajaran_id').val(pembelajaran_id);
	if(ini == ''){
		return false;
	}
	$.ajax({
		url: '{{url('ajax/get-teknik')}}',
		type: 'post',
		data: $("form#form").serialize(),
		success: function(response){
			$('#teknik_penilaian_show').show();
			$('#teknik_penilaian').html('<option value="">== Pilih Teknik Penilaian ==</option>');
			result = checkJSON(response);
			if(result == true){
				var data = $.parseJSON(response);
				if(!$.isEmptyObject(data.result)){
					$.each(data.result, function (i, item) {
						$('#teknik_penilaian').append($('<option>', { 
							value: item.value,
							text : item.text,
						}));
					});
				}
			} else {		
				$('#result').html(response);
			}
		}
	});
});
$('#teknik_penilaian').change(function(){
	var ini = $(this).val();
	if(ini == ''){
		return false;
	}
	var selected = $('#mapel').find('option:selected');
	var pembelajaran_id = selected.data('pembelajaran_id');
	$('#bobot_show').show();
	$.ajax({
		url: '{{url('/ajax/get-kd')}}',
		type: 'post',
		data: $("form#form").serialize(),
		success: function(response){
			$('#result').html(response);
			$.get("{{url('/ajax/get-bobot')}}/"+pembelajaran_id+"/"+ini, function( data ) {
				if(data){
					$('#bobot').val(data);
					$('#bobot_value').val(data);
					$("input#bobot").prop('disabled', true);
				} else {
					$("input#bobot").prop('disabled', false);
				}
			});
		}
	});
});
</script>
@stop