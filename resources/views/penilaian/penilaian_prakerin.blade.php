@extends('adminlte::page')
@section('title_postfix',  'Penilaian '.$title.' |')
@section('content_header')
    <h1>Penilaian {{$title}}</h1>
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
	<form action="{{ route('penilaian.simpan_nilai_ukk') }}" method="post" class="form-horizontal" id="form" accept-charset="UTF-8" enctype="multipart/form-data">
		{{ csrf_field() }}
		<table class="table table-bordered table-hover">
			<thead>
				<tr>
					<th>Mitra DUDI</th>
					<th>Anggota Prakerin</th>
					<th>Waktu Prakerin</th>
					<th>Nilai Prakerin</th>
				</tr>
			</thead>
			<tbody>
			@foreach($all_bimbing_pd as $bimbing_pd)
				<tr>
					<td>{{$bimbing_pd->akt_pd->dudi->nama}}</td>	
				</tr>
			@endforeach
			</tbody>
		</table>
	</form>
@stop

@section('js')
<script>
$('.select2').select2();
</script>
@stop