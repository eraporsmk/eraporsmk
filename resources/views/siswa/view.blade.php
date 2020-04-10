@extends('layouts.modal')

@section('title'){{ $title }} @stop
@section('content')
	<form action="{{ route('siswa.update_data') }}" method="post" id="update_data">
		{{ csrf_field() }}
		<input type="hidden" name="peserta_didik_id" value="{{$siswa->peserta_didik_id}}" />
	<table class="table">
		<tr>
			<td width="30%">Nama</td>
			<td width="1">:</td>
			<td width="70%"><input type="text" class="form-control" value="{{trim($siswa->nama)}}" disabled="disabled" /></td>
		</tr>
		<tr>
			<td width="30%">NIS</td>
			<td width="1">:</td>
			<td width="70%"><input type="text" class="form-control" value="{{$siswa->no_induk}}" disabled="disabled" /></td>
		</tr>
		<tr>
			<td width="30%">NISN</td>
			<td width="1">:</td>
			<td width="70%"><input type="text" class="form-control" value="{{$siswa->nisn}}" disabled="disabled" /></td>
		</tr>
		<tr>
			<td width="30%">NIK</td>
			<td width="1">:</td>
			<td width="70%"><input type="text" class="form-control" value="{{$siswa->nik}}" disabled="disabled" /></td>
		</tr>
		<tr>
			<td width="30%">Jenis Kelamin</td>
			<td width="1">:</td>
			<td width="70%"><input type="text" class="form-control" value="{{($siswa->jenis_kelamin == 'L') ? 'Laki-laki' : 'Perempuan'}}" disabled="disabled" /></td>
		</tr>
		<tr>
			<td width="30%">tempat_lahir</td>
			<td width="1">:</td>
			<td width="70%"><input type="text" class="form-control" value="{{$siswa->tempat_lahir}}, {{CustomHelper::TanggalIndo($siswa->tanggal_lahir)}}" disabled="disabled" /></td>
		</tr>
		<tr>
			<td width="30%">Agama</td>
			<td width="1">:</td>
			<td width="70%"><input type="text" class="form-control" value="{{($siswa->agama) ? $siswa->agama->nama : '-'}}" disabled="disabled" /></td>
		</tr>
		<tr>
			<td width="30%">Status dalam keluarga</td>
			<td width="1">:</td>
			<td width="70%">
				<select name="status" class="form-control select2" style="width:100%">
					<option value="Anak Kandung"{{($siswa->status == 'Anak Kandung') ? ' selected="selected"' : ''}}>Anak Kandung</option>
					<option value="Anak Tiri"{{($siswa->status == 'Anak Tiri') ? ' selected="selected"' : ''}}>Anak Tiri</option>
					<option value="Anak Angkat"{{($siswa->status == 'Anak Angkat') ? ' selected="selected"' : ''}}>Anak Angkat</option>
				</select>
			</td>
		</tr>
		<tr>
			<td width="30%">Anak ke</td>
			<td width="1">:</td>
			<td width="70%"><input type="text" name="anak_ke" class="form-control" value="{{$siswa->anak_ke}}" /></td>
		</tr>
		<tr>
			<td width="30%">Alamat</td>
			<td width="1">:</td>
			<td width="70%"><input type="text" class="form-control" value="{{$siswa->alamat}}" disabled="disabled" /></td>
		</tr>
		<tr>
			<td width="30%">RT/RW</td>
			<td width="1">:</td>
			<td width="70%"><input type="text" class="form-control" value="{{$siswa->rt}}/{{$siswa->rw}}" disabled="disabled" /></td>
		</tr>
		<tr>
			<td width="30%">Desa/Kelurahan</td>
			<td width="1">:</td>
			<td width="70%"><input type="text" class="form-control" value="{{$siswa->desa_kelurahan}}" disabled="disabled" /></td>
		</tr>
		<tr>
			<td width="30%">Kecamatan</td>
			<td width="1">:</td>
			<td width="70%"><input type="text" class="form-control" value="{{$siswa->kecamatan}}" disabled="disabled" /></td>
		</tr>
		<tr>
			<td width="30%">Kodepos</td>
			<td width="1">:</td>
			<td width="70%"><input type="text" class="form-control" value="{{$siswa->kode_pos}}" disabled="disabled" /></td>
		</tr>
		<tr>
			<td width="30%">Telp/HP</td>
			<td width="1">:</td>
			<td width="70%"><input type="text" class="form-control" value="{{$siswa->no_telp}}" disabled="disabled" /></td>
		</tr>
		<tr>
			<td width="30%">Asal Sekolah</td>
			<td width="1">:</td>
			<td width="70%"><input type="text" name="sekolah_asal" class="form-control" value="{{$siswa->sekolah_asal}}" /></td>
		</tr>
		<tr>
			<td width="30%">Diterima dikelas <a class="diterima_dikelas btn btn-sm btn-info" href="{{route('diterima_dikelas', ['id' => $siswa->peserta_didik_id_dapodik])}}"><i class="fa fa-refresh"></i></a></td>
			<td width="1">:</td>
			<td width="70%"><input type="text" id="diterima_kelas" name="diterima_kelas" class="form-control" value="{{$siswa->diterima_kelas}}" /></td>
		</tr>
		<tr>
			<td width="30%">Diterima pada tanggal</td>
			<td width="1">:</td>
			<td width="70%"><input type="text" class="form-control" value="{{$siswa->diterima}}" disabled="disabled" /></td>
		</tr>
		<tr>
			<td width="30%">Email</td>
			<td width="1">:</td>
			<td width="70%"><input type="text" name="email" class="form-control" value="{{$siswa->email}}" /></td>
		</tr>
		<tr>
			<td width="30%">Nama Ayah</td>
			<td width="1">:</td>
			<td width="70%"><input type="text" class="form-control" value="{{$siswa->nama_ayah}}" disabled="disabled" /></td>
		</tr>
		<tr>
			<td width="30%">Pekerjaan Ayah</td>
			<td width="1">:</td>
			<td width="70%"><input type="text" class="form-control" value="{{($siswa->pekerjaan_ayah) ? $siswa->pekerjaan_ayah->nama : '-'}}" disabled="disabled" /></td>
		</tr>
		<tr>
			<td width="30%">Nama Ibu</td>
			<td width="1">:</td>
			<td width="70%"><input type="text" class="form-control" value="{{$siswa->nama_ibu}}" disabled="disabled" /></td>
		</tr>
		<tr>
			<td width="30%">Pekerjaan Ibu</td>
			<td width="1">:</td>
			<td width="70%"><input type="text" class="form-control" value="{{($siswa->pekerjaan_ibu) ? $siswa->pekerjaan_ibu->nama : '-'}}" disabled="disabled" /></td>
		</tr>
		<tr>
			<td width="30%">Nama Wali</td>
			<td width="1">:</td>
			<td width="70%"><input type="text" name="nama_wali" class="form-control" value="{{$siswa->nama_wali}}" /></td>
		</tr>
		<tr>
			<td width="30%">Alamat Wali</td>
			<td width="1">:</td>
			<td width="70%"><input type="text" name="alamat_wali" class="form-control" value="{{$siswa->alamat_wali}}" /></td>
		</tr>
		<tr>
			<td width="30%">Telp/HP Wali</td>
			<td width="1">:</td>
			<td width="70%"><input type="text" name="telp_wali" class="form-control" value="{{$siswa->telp_wali}}" /></td>
		</tr>
		<tr>
			<td width="30%">Pekerjaan Wali</td>
			<td width="1">:</td>
			<td width="70%">
				<select name="kerja_wali" class="form-control select2" style="width:100%">
					@foreach($pekerjaan as $kerja)
					<option value="{{$kerja->pekerjaan_id}}"{{($siswa->kerja_wali == $kerja->pekerjaan_id) ? ' selected="selected"' : ''}}>{{$kerja->nama}}</option>
					@endforeach
				</select>
			</td>
		</tr>
	</table>
	</form>
	<?php
	//dd($siswa); 
	?>
@stop

@section('footer')
	@role(['admin', 'wali', 'waka'])
	<a class="btn btn-primary update_data" href="javascript:void(0)">Simpan</a>
	@endrole
    <a class="btn btn-default" href="javascript:void(0)" data-dismiss="modal">Close</a>
@endsection

@section('js')
<script>
$('.select2').select2();
$('.diterima_dikelas').click(function(e){
	e.preventDefault();
	var url = $(this).attr('href');
	$.ajax({
		url: url,
		type: 'get',
		success: function(response){
			$('#diterima_kelas').val(response);
		}
	});
});
$('.update_data').click(function(e){
	e.preventDefault();
	$.ajax({
		url: '{{ route('siswa.update_data') }}',
		type: 'post',
		data: $('#update_data').serialize(),
		success: function(response){
			var data = $.parseJSON(response);
			if(data.sukses){
				$('#modal_content').modal('toggle');
				swal({title: data.title, text: data.text,icon: data.icon, closeOnClickOutside: false});
			}
		}
	});
});
</script>
@endsection