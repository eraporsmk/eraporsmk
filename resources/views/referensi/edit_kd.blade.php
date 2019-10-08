@extends('layouts.modal')

@section('title') Ubah Ringkasan Kompetensi Dasar @stop



@section('content')
<form id="update_data">
	{{ csrf_field() }}
	<input type="hidden" name="id" value="{{$kd->kompetensi_dasar_id}}" />
	<table class="table">
		<tr>
			<td width="20%">Mata Pelajaran</td>
			<td width="80%">{{$kd->mata_pelajaran->nama}}</td>
		</tr>
		<tr>
			<td>ID KD</td>
			<td><input type="text" class="form-control" value="{{$kd->id_kompetensi}}" disabled="disabled" /></td>
		</tr>
		<tr>
			<td>Kompetensi Dasar</td>
			<td><textarea name="kompetensi_dasar" class="editor form-control" rows="5" required placeholder="Kompetensi Dasar">{{$kd->kompetensi_dasar}} </textarea></td>
		</tr>
		<tr>
			<td>Ringkasan Kompetensi Dasar</td>
			<td><textarea name="kompetensi_dasar_alias" class="editor form-control" rows="5" required placeholder="Ringkasan Kompetensi Dasar">{{$kd->kompetensi_dasar_alias}}</textarea></td>
		</tr>
	</table>
</form>
@stop

@section('footer')
	<a class="btn btn-primary update_data" href="javascript:void(0)">Simpan</a>
    <a class="btn btn-default" href="javascript:void(0)" data-dismiss="modal">Tutup</a>
@endsection

@section('js')
<script type="text/javascript">
$('.select2').select2();
$('.update_data').click(function(e){
	e.preventDefault();
	$.ajax({
		url: '{{ route('referensi.update_kd') }}',
		type: 'post',
		data: $('#update_data').serialize(),
		success: function(response){
			var data = $.parseJSON(response);
			$('#modal_content').modal('toggle');
			swal(data.text, {icon: data.icon,}).then((result) => {
				$('#datatable').DataTable().ajax.reload(null, false);
			});
		}
	});
});
</script>
@Stop