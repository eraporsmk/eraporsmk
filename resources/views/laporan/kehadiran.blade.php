@extends('adminlte::page')

@section('title_postfix', 'Data Ketidakhadiran Peserta Didik |')

@section('content_header')
    <h1>Data Ketidakhadiran Peserta Didik</h1>
@stop
@section('content_header_right')
	<a href="{{url('laporan/unduh-kehadiran/'.$rombongan_belajar_id)}}" class="btn btn-success pull-right"><i class="fa fa-download"></i> Unduh Rekap</a>
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
<form action="{{ route('laporan.simpan_ketidakhadiran') }}" method="post" class="form-horizontal">
	{{ csrf_field() }}
	<input type="hidden" name="sekolah_id" value="{{$user->sekolah_id}}" />
	<div class="table-responsive no-padding">
		<table class="table table-bordered">
			<thead>
				<tr>
					<th width="70%">Nama Peserta Didik</th>
					<th width="10%">Sakit</th>
					<th width="10%">Izin</th>
					<th width="10%">Tanpa Keterangan</th>
				</tr>
			</thead>
			<tbody>
				@foreach($get_siswa as $siswa)
				<tr>
					<td>
						<input type="hidden" name="anggota_rombel_id[]" value="{{$siswa->anggota_rombel_id}}" />
						{{strtoupper($siswa->siswa->nama)}}
					</td>
					<td><input type="number" class="form-control" name="sakit[]" value="{{($siswa->kehadiran) ? $siswa->kehadiran->sakit : ''}}" /></td>
					<td><input type="number" class="form-control" name="izin[]" value="{{($siswa->kehadiran) ? $siswa->kehadiran->izin : ''}}" /></td>
					<td><input type="number" class="form-control" name="alpa[]" value="{{($siswa->kehadiran) ? $siswa->kehadiran->alpa : ''}}" /></td>
				</tr>
				{{--dd($siswa)--}}
				@endforeach
			</tbody>
		</table>
	</div>
	<button type="submit" class="btn btn-success pull-right">Simpan</button>
</form>
@stop