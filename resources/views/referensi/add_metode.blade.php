@extends('adminlte::page')
@if($metode)
@section('title_postfix', 'Ubah Data Referensi Teknik Penilaian |')
@else
@section('title_postfix', 'Tambah Data Referensi Teknik Penilaian |')
@endif
@section('content_header')
	@if($metode)
	<h1>Ubah Data Referensi Teknik Penilaian</h1>
	@else
    <h1>Tambah Referensi Teknik Penilaian</h1>
	@endif
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
	<form action="{{ route('referensi.simpan_metode') }}" method="post" class="form-horizontal" id="form">
		{{ csrf_field() }}
		@if($metode)
		<input type="hidden" name="teknik_penilaian_id" value="{{$metode->teknik_penilaian_id}}" />
		@endif
		<div class="form-group">
			<label for="kompetensi_id" class="col-sm-2 control-label">Kompetensi Penilaian</label>
			<div class="col-sm-5">
				<select name="kompetensi_id" class="select2 form-control"{{($metode) ? ' disabled="disabled"' : ''}}>
					<option value="1"{{($metode) ? ($metode->kompetensi_id == 1) ? ' selected="selected"' : '' : ''}}>Pengetahuan</option>
					<option value="2"{{($metode) ? ($metode->kompetensi_id == 2) ? ' selected="selected"' : '' : ''}}>Keterampilan</option>
				</select>
			</div>
		</div>
		<div class="form-group">
			<label for="nama_metode" class="col-sm-2 control-label">Nama Metode Penilaian</label>
			<div class="col-sm-5">
				<input type="text" class="form-control" name="nama_metode" value="{{($metode) ? $metode->nama : ''}}" />
			</div>
		</div>
		<div class="row">
			<div class="col-md-3 col-md-offset-2">
				<button type="submit" class="btn btn-success">Simpan</button>
			</div>
		</div>
	</form>
@stop
@section('js')
<script type="text/javascript">
$('.select2').select2();
</script>
@stop