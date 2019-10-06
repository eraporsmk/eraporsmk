@extends('adminlte::page')

@section('title_postfix', 'Prestasi Peserta Didik |')

@section('content_header')
    <h1>Tambah Data Prestasi Peserta Didik</h1>
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
<form action="{{ route('laporan.simpan_prestasi') }}" method="post" class="form-horizontal" id="form">
	{{ csrf_field() }}
	<input type="hidden" name="sekolah_id" value="{{$user->sekolah_id}}" />
	<div class="form-group">
		<label for="siswa_id" class="col-sm-2 control-label">Nama Peserta Didik</label>
		<div class="col-sm-5">
			<select name="anggota_rombel_id" class="select2 form-control" id="siswa" required>
				<option value="">== Pilih Nama Peserta Didik ==</option>
				@foreach($get_siswa as $siswa)
				<option value="{{$siswa->anggota_rombel_id}}">{{strtoupper($siswa->siswa->nama)}}</option>
				@endforeach
			</select>
		</div>
	</div>
	<div id="result"></div>
</form>
@stop

@section('js')
<script>
$('.select2').select2();
$('#siswa').change(function(){
	var ini = $(this).val();
	if(ini == ''){
		$('#result').html('');
		return false;
	}
	$('.simpan').hide();
	$.ajax({
		url: '{{url('ajax/get-prestasi')}}',
		type: 'post',
		data: $('#form').serialize(),
		success: function(response){
			$('.simpan').show();
			$('#result').html(response);
		}
	});
});
</script>
@stop