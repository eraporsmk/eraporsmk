@extends('adminlte::page')

@section('title_postfix', 'Laporan Akademik |')

@section('content_header')
    <h1>Catatan Akademik</h1>
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
<form action="{{ route('laporan.simpan_catatan_akademik') }}" method="post" class="form-horizontal">
	{{ csrf_field() }}
	<input type="hidden" name="sekolah_id" value="{{$user->sekolah_id}}" />
	<div class="table-responsive no-padding">
		<table class="table table-bordered">
			<thead>
				<tr>
					<th width="30%">Nama Peserta Didik</th>
					<th width="70%">Deskripsi</th>
				</tr>
			</thead>
			<tbody>
				@foreach($get_siswa as $siswa)
				<tr>
					<td>
						<input type="hidden" name="anggota_rombel_id[]" value="{{$siswa->anggota_rombel_id}}" />
						{{strtoupper($siswa->siswa->nama)}}<br />
						NISN : {{strtoupper($siswa->siswa->nisn)}}<br />
						<span class="label label-success">3 (Tiga) Nilai Akhir Terendah</span>
						<table class="table table-bordered">
							<thead>
								<tr>
									<th style="vertical-align:middle;" rowspan="2">Mata Pelajaran</th>
									<th class="text-center" colspan="3">Nilai</th>
								</tr>
								<tr>
									<th class="text-center">P</th>
									<th class="text-center">K</th>
									<th class="text-center">R</th>
								</tr>
							</thead>
							<tbody>
								@if($siswa->nilai_rapor->count())
								@foreach($siswa->nilai_rapor as $nilai_rapor)
									@if($nilai_rapor->pembelajaran)
								<tr>
									<td>{{$nilai_rapor->pembelajaran->nama_mata_pelajaran}}</td>
									<td class="text-center">{{$nilai_rapor->nilai_p}}</td>
									<td class="text-center">{{$nilai_rapor->nilai_k}}</td>
									<td class="text-center">{{number_format((($nilai_rapor->nilai_p * $nilai_rapor->rasio_p) + ($nilai_rapor->nilai_k * $nilai_rapor->rasio_k)) / 100,0) }}</td>
								</tr>
									@endif
								@endforeach
								@else
								<tr>
									<td class="text-center" colspan="4">Belum dilakukan penilaian</td>
								</tr>
								@endif
							</tbody>
						</table>
					</td>
					<td>
						<textarea name="uraian_deskripsi[]" class="form-control" rows="10">{{($siswa->catatan_wali) ? $siswa->catatan_wali->uraian_deskripsi : ''}}</textarea>
					</td>
				</tr>
				{{--dd($siswa)--}}
				@endforeach
			</tbody>
		</table>
	</div>
	{{--dd($get_siswa)--}}
	<button type="submit" class="btn btn-success pull-right">Simpan</button>
</form>
@stop