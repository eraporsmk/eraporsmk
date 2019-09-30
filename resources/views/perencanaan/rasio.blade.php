@extends('adminlte::page')
@section('title_postfix', 'Rasio Nilai Akhir |')
@section('content_header')
    <h1>Rasio Nilai Akhir</h1>
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
	{{--dd($all_pembelajaran)--}}
	@if($all_pembelajaran->count())
    <form action="{{ route('simpan_rasio') }}" method="post">
		{{ csrf_field() }}
		<input type="hidden" class="form-control" name="semester_id" value="{{$semester->semester_id}}" />
		<table class="table table-bordered table-hover">
		<thead>
			<tr>
				<th rowspan="2" style="vertical-align:middle">Nama Mata Pelajaran</th>
				<th width="10%" colspan="2" class="text-center">Rasio</th>
			</tr>
			<tr>
				<th>Pengetahuan</th>
				<th>Keterampilan</th>
			</tr>
		</thead>
		<tbody>
		@foreach($all_pembelajaran as $pembelajaran)
		{{--dd($pembelajaran)--}}
			<tr>
				<td>
					{{$pembelajaran->mata_pelajaran->nama}}
					<input type="hidden" class="form-control" name="mata_pelajaran_id[{{$pembelajaran->mata_pelajaran_id}}]" value="{{$pembelajaran->mata_pelajaran_id}}" />
				</td>
				<td><input type="text" class="form-control" name="rasio_p[{{$pembelajaran->mata_pelajaran_id}}]" value="{{$pembelajaran->rasio_p}}" /></td>
				<td><input type="text" class="form-control" name="rasio_k[{{$pembelajaran->mata_pelajaran_id}}]" value="{{$pembelajaran->rasio_k}}" /></td>
			</tr>
		@endforeach
		</tbody>
		</table>
@section('box-footer')
		<div class="form-group">
			<input class="btn btn-primary" type="submit" value="Simpan">
		</div>
	</form>
@stop
	@else
	<h3>Anda tidak mengampu mata pelajaran</h3>
	@endif
@stop