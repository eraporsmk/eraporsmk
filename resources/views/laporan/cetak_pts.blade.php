@extends('adminlte::page')

@section('title_postfix', 'Cetak Rapor PTS |')

@section('content_header')
    <h1>Cetak Rapor PTS</h1>
@stop
@section('content_header_right')

@stop
@section('content')
<a href="{{url('laporan/unduh-kehadiran')}}" class="btn btn-warning btn-block btn-lg"><i class="fa fa-print"></i> Cetak Semua</a>
	<div class="table-responsive no-padding" style="margin-top:10px;">
		<table class="table table-bordered">
			<thead>
				<tr>
					<th width="70%">Nama Peserta Didik</th>
					<th width="10%" class="text-center">NISN</th>
					<th width="10%" class="text-center">Aksi</th>
				</tr>
			</thead>
			<tbody>
				@foreach($get_siswa->anggota_rombel as $siswa)
				<form action="{{ route('cetak.rapor_pts') }}" method="post" class="form-horizontal">
				{{ csrf_field() }}
				<tr>
					<td>
						<input type="hidden" name="anggota_rombel_id" value="{{$siswa->anggota_rombel_id}}" />
						@foreach(array_filter($all_rencana_penilaian) as $pembelajaran_id => $rencana_penilaian_id)
						<input type="hidden" name="rencana_penilaian[{{$pembelajaran_id}}]" value="{{$rencana_penilaian_id}}" />
						@endforeach
						{{strtoupper($siswa->siswa->nama)}}
					</td>
					<td class="text-center">{{$siswa->siswa->nisn}}</td>
					<td class="text-center"><button type="submit" class="btn btn-success">Cetak</button></td>
				</tr>
				</form>
				{{--dd($siswa)--}}
				@endforeach
			</tbody>
		</table>
	</div>
{{--dd($all_rencana_penilaian)--}}
@stop