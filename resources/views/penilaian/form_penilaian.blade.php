@extends('adminlte::page')
@section('title_postfix',  'Penilaian '.$title.' |')
@section('content_header')
    <h1>Penilaian {{$title}}</h1>
@stop

@section('content')
	{{-- menampilkan error validasi --}}
	@if (count($errors) > 0)
		<div class="alert alert-danger">
			<ul>
			@foreach ($errors->all() as $error)
				<li>{{ $error }}</li>
			@endforeach
			</ul>
		</div>
	@endif
	<form action="{{ route('penilaian.simpan_nilai') }}" method="post" class="form-horizontal" id="form" accept-charset="UTF-8" enctype="multipart/form-data">
		{{ csrf_field() }}
		<div class="col">
			<div class="col-sm-8">
				<div class="form-group">
					<label for="ajaran_id" class="col-sm-3 control-label">Tahun Ajaran</label>
					<div class="col-sm-9">
						<input type="hidden" name="guru_id" id="guru_id" value="{{$user->guru_id}}" />
						<input type="hidden" name="pembelajaran_id" id="pembelajaran_id" value="" />
						<input type="hidden" name="query" id="query" value="{{$query}}" />
						<input type="hidden" name="kompetensi_id" id="kompetensi_id" value="{{$kompetensi_id}}" />
						<input type="hidden" name="semester_id" id="semester_id" value="{{$semester->semester_id}}" />
						<input type="text" class="form-control" value="{{$semester->nama}} (SMT {{$semester->semester}})" readonly />
					</div>
				</div>
				<div class="form-group">
					<label for="kelas" class="col-sm-3 control-label">Tingkat Kelas</label>
					<div class="col-sm-9">
						<select name="kelas" class="select2 form-control" id="kelas" style="width:100%;">
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
						<select name="rombel_id" class="select2 form-control" id="rombel" style="width:100%;">
							<option value="">== Pilih Rombongan Belajar ==</option>
						</select>
					</div>
				</div>
				<div class="form-group" id="mapel_show" style="display:none;">
					<label for="mapel" class="col-sm-3 control-label">Mata Pelajaran</label>
					<div class="col-sm-9">
						<select name="id_mapel" class="select2 form-control" id="mapel" style="width:100%;">
							<option value="">== Pilih Mata Pelajaran ==</option>
						</select>
					</div>
				</div>
				<div class="form-group" id="reset_capaian_kompetensi" style="display:none;">
					<label for="mapel" class="col-sm-3 control-label">Reset Capaian Kompetensi</label>
					<div class="col-sm-9">
						<a href="javascript:void(0);" class="btn btn-warning">Reset Capaian Kompetensi</a>
					</div>
				</div>
				<div class="form-group" id="rencana_show" style="display:none;">
					<label for="rencana" class="col-sm-3 control-label">Rencana Penilaian</label>
					<div class="col-sm-9">
						<select name="rencana_id" class="select2 form-control" id="rencana" style="width:100%;">
							<option value="">== Pilih Rencana Penilaian ==</option>
						</select>
					</div>
				</div>
				<div class="form-group" id="siswa_show" style="display:none;">
					<label for="siswa" class="col-sm-3 control-label">Nama Peserta Didik</label>
					<div class="col-sm-9">
						<select name="siswa_id" class="select2 form-control" id="siswa" style="width:100%;">
							<option value="">== Pilih Nama Peserta Didik ==</option>
						</select>
					</div>
				</div>
				<div class="form-group" id="aspek_penilaian_show" style="display:none;">
					<label for="aspek_penilaian" class="col-sm-3 control-label">Aspek Penilaian</label>
					<div class="col-sm-9">
						<select name="aspek_penilaian" class="select2 form-control" id="aspek_penilaian" style="width:100%;">
							<option value="">== Pilih Aspek Penilaian ==</option>
						</select>
					</div>
				</div>
				<div class="form-group" id="rencana_p5bk" style="display:none;">
					<label for="rencana" class="col-sm-3 control-label">Projek Penilaian</label>
					<div class="col-sm-9">
						<select name="rencana_budaya_kerja_id" class="select2 form-control" id="rencana_budaya_kerja_id" style="width:100%;">
							<option value="">== Pilih Projek Penilaian ==</option>
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
			<input class="btn btn-primary" type="submit" value="Simpan">
			@if($query == 'remedial')
			<a class="btn btn-danger reset_remedial">Reset Remedial</a>
			@endif
		</div>
	</form>
@stop

@section('js')
<script>
var query = $('#query').val();
var url_rombel;
var url_mapel;
$('#reset_capaian_kompetensi').hide();
if(query == 'pengetahuan' || query == 'keterampilan'){
	url_rombel = '{{url('ajax/get-mapel')}}';
	url_mapel = '{{url('ajax/get-rencana')}}';
	$('#mapel_show').show();
	$('#rencana_show').show();
} else if(query == 'sikap'){
	url_rombel = '{{url('ajax/get-siswa')}}';
	url_mapel = '{{url('ajax/get-sikap')}}';
	$('#siswa_show').show();
} else {
	url_rombel = '{{url('ajax/get-mapel')}}';
	url_mapel = '{{url('ajax/get-kompetensi')}}';
	$('#mapel_show').show();
	if(query !== 'pusat-keunggulan'){
		$('#aspek_penilaian_show').show();
	} else {
		url_mapel = '{{url('ajax/get-rencana')}}';
		$('#rencana_show').show();
	}
	if(query == 'capaian-kompetensi'){
		$('#aspek_penilaian_show').hide();
		url_mapel = '{{url('ajax/get-deskripsi-pk')}}';
	}
	if(query == 'projek-profil-pelajar-pancasila-dan-budaya-kerja'){
		$('#mapel_show').hide();
		$('#rencana_p5bk').show();
		$('#aspek_penilaian_show').hide();
		url_rombel = '{{url('ajax/get-rencana-p5bk')}}';
	}
}
console.log(query);
console.log(url_mapel);
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
		//url: '{{url('ajax/get-mapel')}}',
		url : url_rombel,
		type: 'post',
		data: $("form#form").serialize(),
		success: function(response){
			result = checkJSON(response);
			if(result == true){
				var data = $.parseJSON(response);
				/*if(data.kurikulum === 2021){
					$('#kompetensi_id').val(3);
				}*/
				$('#mapel').html('<option value="">== Pilih Mata Pelajaran ==</option>');
				$('#siswa').html('<option value="">== Pilih Nama Peserta Didik ==</option>');
				$('#rencana_budaya_kerja_id').html('<option value="">== Pilih Projek Penilaian ==</option>');
				if(!$.isEmptyObject(data.mapel)){
					$.each(data.mapel, function (i, item) {
						$('#mapel').append($("<option></option>")
						.attr("value",item.value)
						.attr("data-pembelajaran_id",item.pembelajaran_id)
						.text(item.text)); 
					});
				}
				if(!$.isEmptyObject(data.results)){
					$.each(data.results, function (i, item) {
						$('#rencana_budaya_kerja_id').append($('<option>', { 
							value: item.value,
							text : item.text,
						}));
					});
				}
				if(!$.isEmptyObject(data.siswa)){
					$.each(data.siswa, function (i, item) {
						$('#siswa').append($('<option>', { 
							value: item.value,
							text : item.text,
						}));
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
	console.log(ini);
	var selected = $(this).find('option:selected');
	var pembelajaran_id = selected.data('pembelajaran_id');
	$('#pembelajaran_id').val('');
	$('#pembelajaran_id').val(pembelajaran_id);
	if(ini == ''){
		return false;
	}
	$.ajax({
		url: url_mapel,
		type: 'post',
		data: $("form#form").serialize(),
		success: function(response){
			$('#rencana').html('<option value="">== Pilih Rencana Penilaian ==</option>');
			$('#aspek_penilaian').html('<option value="">== Pilih Aspek Penilaian ==</option>');
			if(query == 'capaian-kompetensi'){
				$('#reset_capaian_kompetensi').show();
			}
			result = checkJSON(response);
			if(result == true){
				var data = $.parseJSON(response);
				console.log(data);
				if(!$.isEmptyObject(data.result)){
					$.each(data.result, function (i, item) {
						$('#rencana').append($('<option>', { 
							value: item.value,
							text : item.text,
						}));
					});
				}
				if(!$.isEmptyObject(data.aspek_penilaian)){
					$.each(data.aspek_penilaian, function (i, item) {
						$('#aspek_penilaian').append($('<option>', { 
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
$('#rencana').change(function(){
	var ini = $(this).val();
	if(ini == ''){
		return false;
	}
	$.ajax({
		url: '{{url('/ajax/get-kd-nilai')}}',
		type: 'post',
		data: $("form#form").serialize(),
		success: function(response){
			$('#simpan').show();
			$('#result').html(response);
		}
	});
});
$('#aspek_penilaian').change(function(){
	var ini = $(this).val();
	if(ini == ''){
		return false;
	}
	$.ajax({
		url: '{{url('/ajax/get-remedial')}}',
		type: 'post',
		data: $("form#form").serialize(),
		success: function(response){
			$('#simpan').show();
			$('#result').html(response);
		}
	});
});
$('#siswa').change(function(){
	var ini = $(this).val();
	if(ini == ''){
		return false;
	}
	$.ajax({
		url: '{{url('/ajax/get-sikap')}}',
		type: 'post',
		data: $("form#form").serialize(),
		success: function(response){
			$('#simpan').hide();
			$('#result').html(response);
		}
	});
});
$( "#form" ).submit(function(e) {
	e.preventDefault();
	$.ajax({
		url:$(this).attr("action"),
		type:$(this).attr("method"),
		data: $(this).serialize(),
		success: function(response){
		var data = $.parseJSON(response);
			$('#rumus').html(data.rumus);
			if(!$.isEmptyObject(data.rerata)){
				$.each(data.rerata, function (i, item) {
					$('#rerata_'+i).val(item.value);
					$('#rerata_jadi_'+i).val(item.rerata_jadi);
					$('#rerata_text_'+i).html(item.rerata_text);
				});
			}
			swal({title: data.title, text: data.text,icon: data.icon, closeOnClickOutside: false}).then(results => {
				if(data.redirect){
					window.location.replace('{{url('penilaian')}}'+data.redirect);
				}
			});
		}
	});
});
$('#reset_capaian_kompetensi').click(function(){
	swal({
		title: "Anda Yakin?",
		text: "Semua isian capaian kompetensi akan dihapus!",
		icon: "warning",
		buttons: true,
		dangerMode: true,
		closeOnClickOutside: false,
	}).then((willDelete) => {
		if (willDelete) {
			$.ajax({
				url:'{{route('penilaian.reset_capaian_kompetensi')}}',
				type:'post',
				data: $('#form').serialize(),
				success: function(data){
					//var data = $.parseJSON(response);
					//console.log(response);
					swal({title: data.title, text: data.text,icon: data.icon, closeOnClickOutside: false}).then(results => {
						window.location.replace('{{route('penilaian.form_penilaian', ['kompetensi_id' => $query])}}');
					});
				}
			});
		}
	});
});
$('.reset_remedial').click(function(){
	swal({
		title: "Anda Yakin?",
		text: "Semua penilaian remedial di pembelajaran terpilih akan dihapus!",
		icon: "warning",
		buttons: true,
		dangerMode: true,
		closeOnClickOutside: false,
	}).then((willDelete) => {
		if (willDelete) {
			$.ajax({
				url:'{{route('penilaian.reset_remedial')}}',
				type:'post',
				data: $('#form').serialize(),
				success: function(data){
					//var data = $.parseJSON(response);
					//console.log(response);
					swal({title: data.title, text: data.text,icon: data.icon, closeOnClickOutside: false}).then(results => {
						window.location.replace('{{route('penilaian.form_penilaian', ['kompetensi_id' => $query])}}');
					});
				}
			});
		}
	});
});
$('#rencana_budaya_kerja_id').change(function(){
	var ini = $(this).val();
	if(ini == ''){
		return false;
	}
	$.ajax({
		url: '{{url('/ajax/get-form-p5bk')}}',
		type: 'post',
		data: $("form#form").serialize(),
		success: function(response){
			$('#simpan').show();
			$('#result').html(response);
		}
	});
});
</script>
@stop