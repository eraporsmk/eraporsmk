@extends('adminlte::page')

@section('title_postfix', 'Data Nilai US/USBN Peserta Didik |')

@section('content_header')
<h1>Data Nilai US/USBN Peserta Didik</h1>
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
@if ($errors->any())
<div class="alert alert-danger">
	<ul>
		@foreach ($errors->all() as $error)
		<li>{{ $error }}</li>
		@endforeach
	</ul>
</div>
@endif
@if($rombongan_belajar)
<form action="{{ route('laporan.nilai_us') }}" method="post" class="form-horizontal" id="form">
	{{ csrf_field() }}
	<div class="col">
		<div class="col-sm-8">
			<div class="form-group">
				<label for="ajaran_id" class="col-sm-3 control-label">Tahun Ajaran</label>
				<div class="col-sm-9">
					<input type="hidden" name="guru_id" value="{{$user->guru_id}}" />
					<input type="hidden" name="sekolah_id" value="{{$user->sekolah_id}}" />
					<input type="hidden" name="query" id="query" value="nilai_us" />
					<input type="hidden" name="semester_id" id="semester_id" value="{{$semester->semester_id}}" />
					<input type="hidden" name="rombel_id" id="rombel_id" value="{{$rombongan_belajar->rombongan_belajar_id}}" />
					<input type="text" class="form-control" value="{{$semester->nama}} (SMT {{$semester->semester}})"
						readonly />
				</div>
			</div>
			<div class="form-group">
				<label for="pembelajaran_id" class="col-sm-3 control-label">Pembelajaran</label>
				<div class="col-sm-9">
					<select name="pembelajaran_id" class="select2 form-control" id="pembelajaran_id">
						<option value="">== Pilih Pembelajaran ==</option>
						@foreach ($rombongan_belajar->pembelajaran as $pembelajaran)
						<option value="{{$pembelajaran->pembelajaran_id}}">{{$pembelajaran->nama_mata_pelajaran}}</option>
						@endforeach
					</select>
				</div>
			</div>
			<div class="form-group template" style="display: none;">
				<label for="template" class="col-sm-3 control-label">Unduh Template</label>
				<div class="col-sm-9">
					<div class="row">
						<div class="col-sm-6">
							<a href="javascript:void(0)" class="btn btn-block btn-success btn-flat template_excel">Unduh Template</a>
						</div>
						<div class="col-sm-6">
							<span class="btn btn-danger btn-file btn-flat btn-block"> Unggah Excel <input type="file" id="fileupload" name="file" /></span>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
	<div style="clear:both;"></div>
	<div id="result"></div>
	<button type="submit" class="btn-submit btn btn-success pull-right" style="display:none;">Simpan</button>
</form>
@else
<div class="alert alert-danger alert-block"><i class="fa fa-ban"></i>
	<strong>Akses Ditutup!</strong> Anda tidak menjadi wali kelas tingkat akhir
</div>
@endif
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
$('#pembelajaran_id').change(function(){
	var ini = $(this).val();
	if(ini == ''){
		return false;
	}
	$.ajax({
		url: '{{url('ajax/get-nilai-us')}}',
		type: 'post',
		data: $("form#form").serialize(),
		success: function(response){
			$('#result').html(response);
			$('.btn-submit').show();
			$('.template').show();
			$('.template_excel').attr('href', '{{url('laporan/unduh-template/nilai-us')}}/'+ini);
		}
	});
});
</script>
@stop