@extends('adminlte::page')

@section('title_postfix', 'Praktik Kerja Lapangan |')

@section('content_header')
<h1>Praktik Kerja Lapangan</h1>
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
<form action="{{ route('laporan.simpan_pkl') }}" method="post" class="form-horizontal">
	{{ csrf_field() }}
	<input type="hidden" name="sekolah_id" value="{{$user->sekolah_id}}" />
	<div class="table-responsive no-padding">
		<table class="table table-bordered">
			<thead>
				<tr>
					<th class="text-center" style="vertical-align:middle;">Nama Peserta Didik</th>
					<th class="text-center" style="vertical-align:middle;">Mitra DU/DI</th>
					<th class="text-center" style="vertical-align:middle;">Alamat</th>
					<!--th class="text-center" style="vertical-align:middle;">Bidang Usaha</th-->
					<th class="text-center" style="vertical-align:middle;">Skala Kesesuaian dengan Kompetensi Keahlian (1-10)</th>
					<th class="text-center" style="vertical-align:middle;">Lamanya (bulan)</th>
					<th class="text-center" style="vertical-align:middle;">Keterangan</th>
				</tr>
			</thead>
			<tbody>
				@foreach($get_siswa as $siswa)
				<tr>
					<td>
						<input type="hidden" name="anggota_rombel_id[]" value="{{$siswa->anggota_rombel_id}}" />
						{{strtoupper($siswa->siswa->nama)}}
					</td>
					<td>
						<select class="form-control select2" name="mitra_prakerin[]" id="mitra_prakerin"
							style="width:100%">
							<option value="">== Pilih DU/DI ==</option>
							@foreach($all_dudi as $dudi)
							<option value="{{$dudi->nama}}"
								data-lokasi="{{$dudi->desa_kelurahan}} - {{$dudi->kecamatan->nama}} - {{$dudi->kecamatan->get_kabupaten->nama}}"
								{{($siswa->prakerin) ? ($siswa->prakerin->mitra_prakerin == $dudi->nama) ? ' selected="selected"' : '' : ''}}>
								{{$dudi->nama}}</option>
							@endforeach
						</select>
					</td>
					<td>
						<input type="text" class="form-control" name="lokasi_prakerin[]" id="lokasi_prakerin"
							value="{{($siswa->prakerin) ? $siswa->prakerin->lokasi_prakerin : ''}}" />
					</td>
					<!--td>
						<input type="text" class="form-control" name="bidang_usaha[]" id="bidang_usaha" value="{{($siswa->prakerin) ? $siswa->prakerin->bidang_usaha : ''}}" />
					</td-->
					<td>
						<select class="form-control select2" name="skala[]" id="skala" style="width:100%">
							<option value="">== Pilih Skala ==</option>
							@for ($i = 1; $i < 11; $i++)
							<option value="{{$i}}"{{($siswa->prakerin) ? ($siswa->prakerin->skala == $i) ? ' selected="selected"' : '' : ''}}>{{$i}}</option>
							@endfor
						</select>
					</td>
					<td>
						<input type="number" class="form-control" name="lama_prakerin[]" value="{{($siswa->prakerin) ? $siswa->prakerin->lama_prakerin : ''}}" />
					</td>
					<td>
						<input type="text" class="form-control" name="keterangan_prakerin[]" value="{{($siswa->prakerin) ? $siswa->prakerin->keterangan_prakerin : ''}}" />
					</td>
				</tr>
				@endforeach
			</tbody>
		</table>
	</div>
	<button type="submit" class="btn btn-success pull-right">Simpan</button>
</form>
@stop

@section('js')
<script>
	$('.select2').select2();
$('select#mitra_prakerin').change(function(e) {
	e.preventDefault();
	var ini = $(this).val();
	var lokasi_prakerin = $(this).find("option:selected").data('lokasi');
	if(ini == ''){
		$(this).closest('td').next('td').find('input').val('');
	} else {
		$(this).closest('td').next('td').find('input').val(lokasi_prakerin);
	}
});
</script>
@stop