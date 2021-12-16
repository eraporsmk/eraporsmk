@extends('adminlte::page')
@section('title_postfix', 'Pengaturan Umum | ')
@section('content_header')
    <h1>Pengaturan Umum</h1>
@stop

@section('content')
	@if ($message = Session::get('success'))
      <div class="alert alert-success alert-block alert-dismissable"><i class="fa fa-check"></i>
        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
          <strong>Sukses!</strong> {{ $message }}
      </div>
    @endif

    @if (count($errors) > 0)
		<div class="alert alert-danger">
			<ul>
				@foreach ($errors->all() as $error)
					<li>{{ $error }}</li>
				@endforeach
			</ul>
		</div>
	@endif
	<form action="konfigurasi/simpan" method="post"  enctype="multipart/form-data">
		{{ csrf_field() }}
		<div class="col-xs-12 no-padding">
			<div class="form-group col-md-8">
				<div class="form-group">as
					<label for="semester_id">Periode Aktif</label>
					<select class="select2 form-control" name="semester_id" style="width: 100%;">
					@foreach ($all_data as $tahun)
						@foreach ($tahun->semester as $data)
						<option value="{{ $data->semester_id }}" {{ ((old('semester_id')) ? old('semester_id') == $data->semester_id : ($data->periode_aktif == 1 ? "selected":"")) }}>{{ $data->nama }} - Semester {{ ($data->semester == 1) ? 'Ganjil' : 'Genap' }}</option>
						@endforeach
					@endforeach
					</select>
				</div>
				<div class="form-group">
					<label for="tanggal_rapor">Tanggal Rapor</label>
					<div class="input-group">
						<div class="input-group-addon"><i class="fa fa-calendar"></i></div>
						<input name="tanggal_rapor" value="{{ (old('tanggal_rapor')) ? old('tanggal_rapor') : date('d-m-Y', strtotime(config('global.tanggal_rapor'))) }}" id="tanggal_rapor" class="form-control datepicker" data-date-format="dd-mm-yyyy" type="text">
					</div>
				</div>
				<div class="form-group">
					<label for="description">Zona Waktu</label>
					<select class="form-control select2" name="zona" style="width: 100%;">
						<option value="1"{{ ((old('zona')) ? old('zona') == 1 : (config('global.zona') == 1 ? "selected":"")) }}>Waktu Indonesia Barat (WIB)</option>
						<option value="2"{{ ((old('zona')) ? old('zona') == 2 : (config('global.zona') == 2 ? "selected":"")) }}>Waktu Indonesia Tengah (WITA)</option>
						<option value="3"{{ ((old('zona')) ? old('zona') == 3 : (config('global.zona') == 3 ? "selected":"")) }}>Waktu Indonesia Timur (WIT)</option>
					</select>
				</div>
				<div class="form-group">
					<label for="guru_id">Kepala Sekolah</label>
					<select class="form-control select2" name="guru_id" style="width: 100%;">
					@foreach ($all_guru as $guru)
						<option title="{{$guru->nuptk}}" value="{{ $guru->guru_id }}" {{ ((old('guru_id')) ? old('guru_id') == $data->guru_id : ($guru->guru_id == $sekolah->guru->guru_id ? "selected":"")) }}>{{ CustomHelper::nama_guru($guru->gelar_depan, $guru->nama, $guru->gelar_belakang) }}</option>
					@endforeach
					</select>
				</div>
				<div class="form-group">
					<label for="empat_tahun">Rombongan Belajar 4 Tahun</label>
					<select class="form-control select2" name="empat_tahun[]" style="width: 100%;" multiple>
					@foreach ($all_rombel as $rombel)
						<option value="{{$rombel->rombongan_belajar_id}}" {{ (in_array($rombel->rombongan_belajar_id, $rombel_4_tahun)) ? "selected":"" }}>{{ $rombel->nama }}</option>
					@endforeach
					</select>
				</div>
			</div>
			<div class="form-group col-md-4 text-center">
				<label for="logo">Logo Sekolah</label>
				<div class="row">
					<div class="col-sm-12">
						<div class="form-group">
							<?php $img = ($sekolah->logo_sekolah!= '')  ? asset('storage/images/245/'.$sekolah->logo_sekolah) : asset('vendor/img/logo.png'); ?><img src="<?php echo $img;?>" alt="logo_sekolah" />
							<div class="input-group" style="margin-top:10px;">
							<label class="input-group-btn">
								<span class="btn btn-primary">
									Cari berkas&hellip; <input type="file" name="logo_sekolah" style="display: none;" multiple>
								</span>
							</label>
							<input type="text" class="form-control" readonly>
						</div>
				</div>
			</div>
		</div>
			</div>
		</div>
		<div class="col-xs-12">
			<div class="form-group">
				<input type="hidden" name="sekolah_id" value="{{ $sekolah_id }}" />
				<input class="btn btn-primary" type="submit" value="Simpan">
			</div>
		</div>
	</form>
@stop
@section('js')
<script>
function formatState (state) {
	if (!state.id) {
		return state.text;
	}
	var $state = $(
		'<span> ' + state.text + '<br /> '+ state.title +'</span>'
	);
  return $state;
};
$('.select2').select2({
	templateResult: formatState,
});
//$('.select2').select2();
$('.datepicker').datepicker({
	autoclose: true,
	format: "dd-mm-yyyy",
});
	$(document).on('change', ':file', function() {
    var input = $(this),
        numFiles = input.get(0).files ? input.get(0).files.length : 1,
        label = input.val().replace(/\\/g, '/').replace(/.*\//, '');
    input.trigger('fileselect', [numFiles, label]);
  });

  // We can watch for our custom `fileselect` event like this
  $(document).ready( function() {
      $(':file').on('fileselect', function(event, numFiles, label) {

          var input = $(this).parents('.input-group').find(':text'),
              log = numFiles > 1 ? numFiles + ' files selected' : label;

          if( input.length ) {
              input.val(log);
          } else {
              if( log ) alert(log);
          }

      });
  });
</script>
@Stop