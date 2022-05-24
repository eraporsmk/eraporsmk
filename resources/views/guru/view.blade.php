@extends('layouts.modal')

@section('title'){{ $title }} @stop



@section('content')
<?php
$disabled = 'disabled="disabled"';
if($guru->jenis_ptk_id == 97 || $guru->jenis_ptk_id == 98){
	$disabled = '';
}
$get_gelar_belakang = array();
$get_gelar_depan = array();
$get_kode_depan = array();
$get_kode_belakang = array();
if($guru->gelar_depan){
	foreach($guru->gelar_depan as $gelar_depan){
		$get_kode_depan[] = $gelar_depan->kode;
		$get_gelar_depan[] = $gelar_depan->gelar_akademik_id;
	}
}
if($guru->gelar_belakang){
	foreach($guru->gelar_belakang as $gelar_belakang){
		$get_kode_belakang[] = $gelar_belakang->kode;
		$get_gelar_belakang[] = $gelar_belakang->gelar_akademik_id;
	}
}
?>
	<form action="{{ route('guru.update_data') }}" method="post" id="update_data">
		{{ csrf_field() }}
		<input type="hidden" name="guru_id" value="{{$guru->guru_id}}" />
	<table class="table">
		<tr>
			<td width="30%">Nama</td>
			<td width="1">:</td>
			<td width="70%"><input type="text" name="nama" class="form-control" value="{{strtoupper(trim($guru->nama))}}" {{$disabled}} /></td>
		</tr>
		<tr>
			<td width="30%">Gelar Depan</td>
			<td width="1">:</td>
			<td width="70%">
				@role('admin')
				<select name="gelar_depan[]" class="form-control select2" style="width:100%" multiple="multiple">
					@foreach($ref_gelar_depan as $depan)
					<option value="{{$depan->gelar_akademik_id}}"{{(in_array($depan->gelar_akademik_id,$get_gelar_depan)) ? ' selected="selected"' : ''}}>{{$depan->kode}}</option>
					@endforeach
				</select>
				@endrole
				@role('guru')
				{{implode(', ',$get_kode_depan)}}
				@endrole
			</td>
		</tr>
		<tr>
			<td width="30%">Gelar Belakang</td>
			<td width="1">:</td>
			<td width="70%">
				@role('admin')
				<select name="gelar_belakang[]" class="form-control select2" style="width:100%" multiple="multiple">
					@foreach($ref_gelar_belakang as $belakang)
					<option value="{{$belakang->gelar_akademik_id}}"{{(in_array($belakang->gelar_akademik_id,$get_gelar_belakang)) ? ' selected="selected"' : ''}}>{{$belakang->kode}}</option>
					@endforeach
				</select>
				@endrole
				@role('guru')
				{{implode(', ',$get_kode_belakang)}}
				@endrole
			</td>
		</tr>
		<tr>
			<td width="30%">NUPTK</td>
			<td width="1">:</td>
			<td width="70%"><input type="text" class="form-control" name="nuptk" value="{{$guru->nuptk}}" {{$disabled}} /></td>
		</tr>
		<tr>
			<td width="30%">NIP</td>
			<td width="1">:</td>
			<td width="70%"><input type="text" class="form-control" name="nip" value="{{$guru->nip}}" {{$disabled}} /></td>
		</tr>
		<tr>
			<td width="30%">NIK</td>
			<td width="1">:</td>
			<td width="70%"><input type="text" class="form-control" name="nik" value="{{$guru->nik}}" {{$disabled}} /></td>
		</tr>
		<tr>
			<td width="30%">Jenis Kelamin</td>
			<td width="1">:</td>
			<td width="70%">
				<select class="form-control select2" style="width:100%" name="jenis_kelamin" {{$disabled}}>
					<option value="L" {{($guru->jenis_kelamin == 'L') ? 'selected="selected"' : ''}}>Laki-laki</option>
					<option value="P" {{($guru->jenis_kelamin == 'P') ? 'selected="selected"' : ''}}>Perempuan</option>
				</select>
			</td>
		</tr>
		<tr>
			<td width="30%">Tempat Lahir</td>
			<td width="1">:</td>
			<td width="70%"><input type="text" name="tempat_lahir" class="form-control" value="{{$guru->tempat_lahir}}" {{$disabled}} /></td>
		</tr>
		<tr>
			<td width="30%">Tanggal Lahir</td>
			<td width="1">:</td>
			<td width="70%"><input type="text" name="tanggal_lahir" class="form-control datepicker" value="{{date('m/d/Y', strtotime($guru->tanggal_lahir))}}" {{$disabled}} /></td>
		</tr>
		<tr>
			<td width="30%">Agama</td>
			<td width="1">:</td>
			<td width="70%">
				<select name="agama_id" class="form-control select2" style="width:100%" {{$disabled}}>
					@foreach($ref_agama as $agama)
					@if($agama->id)
					<option value="{{$agama->id}}"{{($guru->agama_id == $agama->id) ? ' selected="selected"' : ''}}>{{$agama->nama}}</option>
					@else
					<option value="{{$agama->agama_id}}"{{($guru->agama_id == $agama->agama_id) ? ' selected="selected"' : ''}}>{{$agama->nama}}</option>
					@endif
					@endforeach
				</select>
			</td>
		</tr>
		<tr>
			<td width="30%">Alamat</td>
			<td width="1">:</td>
			<td width="70%"><input type="text" class="form-control" name="alamat" value="{{$guru->alamat}}" {{$disabled}} /></td>
		</tr>
		<tr>
			<td width="30%">RT</td>
			<td width="1">:</td>
			<td width="70%"><input type="text" class="form-control" name="rt" value="{{$guru->rt}}" {{$disabled}} /></td>
		</tr>
		<tr>
			<td width="30%">RW</td>
			<td width="1">:</td>
			<td width="70%"><input type="text" class="form-control" name="rw" value="{{$guru->rw}}" {{$disabled}} /></td>
		</tr>
		<tr>
			<td width="30%">Desa/Kelurahan</td>
			<td width="1">:</td>
			<td width="70%"><input type="text" class="form-control" name="desa_kelurahan" value="{{$guru->desa_kelurahan}}" {{$disabled}} /></td>
		</tr>
		<tr>
			<td width="30%">Kecamatan</td>
			<td width="1">:</td>
			<td width="70%"><input type="text" class="form-control" name="kecamatan" value="{{$guru->kecamatan}}" {{$disabled}} /></td>
		</tr>
		<tr>
			<td width="30%">Kodepos</td>
			<td width="1">:</td>
			<td width="70%"><input type="text" class="form-control" name="kode_pos" value="{{$guru->kode_pos}}" {{$disabled}} /></td>
		</tr>
		<tr>
			<td width="30%">Telp/HP</td>
			<td width="1">:</td>
			<td width="70%"><input type="text" class="form-control" name="no_hp" value="{{$guru->no_hp}}" {{$disabled}} /></td>
		</tr>
		<tr>
			<td width="30%">Email</td>
			<td width="1">:</td>
			<td width="70%"><input type="text" name="email" class="form-control" value="{{$guru->email}}" /></td>
		</tr>
		<tr>
			<td width="30%">Jenis PTK</td>
			<td width="1">:</td>
			<td width="70%">
				<input type="hidden" class="form-control" value="{{$guru->jenis_ptk_id}}" name="jenis_ptk_id" />
				<input type="text" class="form-control" value="{{($guru->jenis_ptk) ? $guru->jenis_ptk->jenis_ptk : '-'}}" disabled="disabled" />
			</td>
		</tr>
		@if($guru->jenis_ptk_id == 98)
		<tr>
			<td width="30%">Dudi</td>
			<td width="1">:</td>
			<td width="70%">
				@role('admin')
				<select name="dudi_id" class="form-control select2" style="width:100%">
					<option value="">Pilih Dudi</option>
					@foreach($data_dudi as $dudi)
					<option value="{{$dudi->dudi_id}}" {{($guru->dudi && $guru->dudi->dudi_id == $dudi->dudi_id) ? ' selected="selected"' : '-'}}>{{$dudi->nama}}</option>
					@endforeach
				</select>
				@endrole
				@role('guru')
				<input type="text" class="form-control" value="{{($guru->dudi) ? $guru->dudi->nama : '-'}}" disabled="disabled" />
				@endrole
			</td>
		</tr>
		@endif
		<tr>
			<td width="30%">Status Kepegawaian</td>
			<td width="1">:</td>
			<td width="70%"><input type="text" class="form-control" value="{{($guru->status_kepegawaian) ? $guru->status_kepegawaian->nama : '-'}}" disabled="disabled" /></td>
		</tr>
	</table>
	</form>
	<?php //dd($guru->gelar_belakang); ?>
@stop

@section('footer')
	@role('admin')
	@if($guru->jenis_ptk_id == 97 || $guru->jenis_ptk_id == 98)
	<a class="btn btn-danger" href="{{url('guru/hapus/'.$guru->jenis_ptk_id.'/'.$guru->guru_id)}}">Hapus</a>
	@endif
	<a class="btn btn-primary update_data" href="javascript:void(0)">Simpan</a>
	@endrole
    <a class="btn btn-default" href="javascript:void(0)" data-dismiss="modal">Tutup</a>
@endsection

@section('js')
<link rel="stylesheet" href="{{asset('vendor/adminlte/plugins/datepicker/datepicker3.css')}}">
<script src="{{asset('vendor/adminlte/plugins/datepicker/bootstrap-datepicker.js')}}"></script>
<script type="text/javascript">
$('.select2').select2();
$('.datepicker').datepicker({
	autoclose: true
});
$('.update_data').click(function(e){
	e.preventDefault();
	$.ajax({
		url: '{{ route('guru.update_data') }}',
		type: 'post',
		data: $('#update_data').serialize(),
		success: function(data){
			//var data = $.parseJSON(response);
			if(data.sukses){
				$('#modal_content').modal('toggle');
				swal({title: data.title, text: data.text,icon: data.icon, closeOnClickOutside: false}).then((result) => {
					$('#datatable').DataTable().ajax.reload(null, false);
				});
			} else {
				swal({title: data.title, text: data.text,icon: data.icon, closeOnClickOutside: false});
			}
		}
	});
});
</script>
@Stop