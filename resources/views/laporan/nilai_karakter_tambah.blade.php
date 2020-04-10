@extends('adminlte::page')

@section('title_postfix', 'Data Penilaian Karakter | ')

@section('content_header')
    <h1>Data Penilaian Karakter</h1>
@stop

@section('content')
<form action="{{ route('laporan.simpan_nilai_karakter') }}" method="post" class="form-horizontal" id="form">
	{{ csrf_field() }}
	<input type="hidden" name="sekolah_id" value="{{$user->sekolah_id}}" />
	<div class="col-sm-12">
		<div class="form-group">
			<label for="ajaran_id" class="col-sm-3 control-label">Tahun Ajaran</label>
			<div class="col-sm-9">
				<input type="hidden" id="semester_id" name="semester_id" value="{{$semester->semester_id}}" />
				<input type="text" class="form-control" value="{{$semester->nama}} (SMT {{$semester->semester}})" readonly />
			</div>
		</div>
		<div class="form-group">
			<label for="siswa" class="col-sm-3 control-label">Peserta Didik</label>
			<div class="col-sm-9">
				<select name="anggota_rombel_id" class="select2 form-control required" id="anggota_rombel_id"{{($data) ? ' disabled="disabled"' : ''}}>
					@if($data)
					<option value="" data-nama="{{strtoupper($data->siswa->nama)}}">{{strtoupper($data->siswa->nama)}}</option>
					@else
					<option value="">== Pilih Peserta Didik ==</option>
					@foreach($get_siswa as $siswa)
					<option value="{{$siswa->anggota_rombel_id}}" data-nama="{{strtoupper($siswa->siswa->nama)}}">{{strtoupper($siswa->siswa->nama)}}</option>
					@endforeach
					@endif
				</select>
			</div>
		</div>
		@foreach($all_sikap as $key => $sikap)
		<div class="form-group">
			<label for="capaian" class="col-sm-3 control-label">Catatan Penilaian Sikap {{$sikap->butir_sikap}}<br /><b><i><span id="nama_siswa">{{($data) ? strtoupper($data->siswa->nama) : ''}}</span></i></b></label>
			<div class="col-sm-9" id="sugesti_{{$sikap->sikap_id}}">
			</div>
		</div>
		<div class="form-group">
			<label for="sikap_id_19" class="col-sm-3 control-label">{{$sikap->butir_sikap}}
				<ul style="font-weight:normal;">
					@foreach($sikap->sikap as $sub_sikap)
					<li style="font-weight:normal;">{{$sub_sikap->butir_sikap}}</li>
					@endforeach
				</ul>
			</label>
			<div class="col-sm-9">
				<input type="hidden" id="sikap_id" name="sikap_id[{{$sikap->sikap_id}}]" value="{{$sikap->sikap_id}}" />
				<textarea id="sikap_id_{{$sikap->sikap_id}}" name="deskripsi[{{$sikap->sikap_id}}]" class="form-control" rows="6">{{($data) ? ($data->nilai_karakter[$key]) ? $data->nilai_karakter[$key]->deskripsi : '' : ''}}</textarea>
			</div>
		</div>
		@endforeach
		<div class="form-group">
			<label for="capaian" class="col-sm-3 control-label">Catatan Perkembangan Karakter</label>
			<div class="col-sm-9">
			<textarea name="capaian" id="capaian" class="form-control" rows="10" required>{{($data) ? $data->capaian : ''}}</textarea>
			</div>
		</div>
	</div>
	<div class="col-sm-9 col-sm-offset-3 sembunyi" style="display:none;">
		<button type="submit" class="btn btn-success">Simpan</button>
	</div>
</form>
@stop

@section('js')
<script type="text/javascript">
$('.select2').select2();
$('#anggota_rombel_id').change(function(){
	$('.sembunyi').hide();
	var ini = $(this).val();
	if(ini == ''){
		return false;
	}
	$('.sembunyi').show();
	var nama_siswa = $(this).find(':selected').data('nama');
	$.ajax({
		url: '{{url('ajax/get-ppk')}}',
		type: 'post',
		data: $("form#form").serialize(),
		success: function(response){
			var data = $.parseJSON(response);
			$('span[id="nama_siswa"]').html(nama_siswa);
			@foreach($all_sikap as $sikap)
			$('#sikap_id_{{$sikap->sikap_id}}').val(data.sikap_id_{{$sikap->sikap_id}});
			$('#sugesti_{{$sikap->sikap_id}}').html('Catatan nilai sikap dari guru:<br />'+data.sugesti_{{$sikap->sikap_id}});
			$('#capaian').val(data.capaian);
			@endforeach
		}
	});
});
</script>
@Stop