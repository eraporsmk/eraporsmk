@extends('adminlte::page')
@section('title_postfix', 'Penentuan Bobot Penilaian Keterampilan |')
@section('content_header')
    <h1>Penentuan Bobot Penilaian Keterampilan</h1>
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
	<form action="{{ route('simpan_bobot') }}" method="post">
		{{ csrf_field() }}
		<table class="table table-bordered table-hover">
			<thead>
				<tr>
					<th>Teknik Penilaian</th>
					<th>Rombongan Belajar</th>
					<th>Mata Pelajaran</th>
					<th width="10%" class="text-center">Bobot</th>
				</tr>
			</thead>
			<tbody>
				@if($all_bobot->count())
				@foreach($all_bobot as $bobot)
					<tr>
						<td>{{$bobot->metode->nama}}</td>
						<td>{{$bobot->pembelajaran->rombongan_belajar->nama}}</td>
						<td>{{$bobot->pembelajaran->nama_mata_pelajaran}}</td>
						<td>
							<input type="hidden" class="form-control" value="{{$bobot->pembelajaran->pembelajaran_id}}" name="pembelajaran_id[]" />
							<input type="text" class="form-control" value="{{$bobot->bobot}}" name="bobot[{{$bobot->bobot_keterampilan_id}}]" />
						</td>
					</tr>
				@endforeach
				@else
				<tr>
					<td colspan="4" class="text-center">Tidak ada bobot keterampilan tersimpan di database</td>
				</tr>
				@endif
			</tbody>
		</table>
		@if($all_bobot->count())
		<div class="form-group">
			<input class="btn btn-primary" type="submit" value="Simpan">
		</div>
		@endif
	</form>
@stop