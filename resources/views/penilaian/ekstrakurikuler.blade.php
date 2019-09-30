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
	<form action="{{ route('penilaian.simpan_nilai_ekskul') }}" method="post" class="form-horizontal" id="form" accept-charset="UTF-8" enctype="multipart/form-data">
		{{ csrf_field() }}
		<div class="col">
			<div class="col-sm-8">
				<div class="form-group">
					<label for="ajaran_id" class="col-sm-3 control-label">Tahun Ajaran</label>
					<div class="col-sm-9">
						<input type="hidden" name="guru_id" value="{{$user->guru_id}}" />
						<input type="hidden" name="sekolah_id" value="{{$user->sekolah_id}}" />
						<input type="hidden" name="semester_id" id="semester_id" value="{{$semester->semester_id}}" />
						<input type="text" class="form-control" value="{{$semester->nama}} (SMT {{$semester->semester}})" readonly />
					</div>
				</div>
				<div class="form-group">
					<label for="kelas_ekskul" class="col-sm-3 control-label">Ekstrakurikuler</label>
					<div class="col-sm-9">
						<select name="kelas_ekskul" class="select2 form-control" id="kelas_ekskul" style="width:100%;">
							<option value="">== Pilih Nama Ekstrakurikuler ==</option>
							@foreach($all_ekskul as $ekskul)
							<option value="{{$ekskul->rombongan_belajar_id}}">{{$ekskul->nama_ekskul}}</option>
							@endforeach
						</select>
					</div>
				</div>
			</div>
		</div>
		<div style="clear:both;"></div>
		<div id="result"></div>
		<div class="col">
			<div class="col-sm-8" id="simpan" style="display:none;">
				<input class="btn btn-primary" type="submit" value="Simpan">
			</div>
		</div>
	</form>
@stop

@section('js')
<script>
$('.select2').select2();
$('#kelas_ekskul').change(function(){
	$('#simpan').hide();
	$('#result').html('');
	var ini = $(this).val();
	if(ini == ''){
		return false;
	}
	$.ajax({
		url: '{{url('ajax/get-anggota-ekskul')}}',
		type: 'post',
		data: $("form#form").serialize(),
		success: function(response){
			$('#result').html(response);
			$('#simpan').show();
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
			swal({text: data.text,icon: data.icon, closeOnClickOutside: false}).then(results => {
				if(data.redirect){
					window.location.replace('{{url('penilaian')}}/'+data.redirect);
				}
			});
		}
	});
});
</script>
@stop