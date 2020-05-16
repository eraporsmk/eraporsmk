@extends('layouts.modal')

@section('title') Duplikasi Perencanaan @stop

@section('content')
	<form id="update_data">
		{{ csrf_field() }}
		<input type="hidden" name="rencana_id" value="{{$rencana->rencana_penilaian_id}}" />
		<table class="table">
			<tr>
			<td width="30%">Rombongan Belajar</td>
			<td width="1">:</td>
			<td width="70%">
				<select name="pembelajaran_id" class="form-control select2" style="width:100%">
					<option value="">== Pilih Rombongan Belajar ==</option>
					@foreach($rombongan_belajar as $rombel)
					<option value="{{$rombel->one_pembelajaran->pembelajaran_id}}">{{$rombel->nama}}</option>
					@endforeach
				</select>
			</td>
		</tr>
		</table>
	</form>
@endsection
@section('footer')
	<a class="btn btn-primary update_data" href="javascript:void(0)">Simpan</a>
	<a class="btn btn-default" href="javascript:void(0)" data-dismiss="modal">Tutup</a>
@endsection
@section('js')
<script type="text/javascript">
$('.select2').select2();
$('.datepicker').datepicker({
	autoclose: true
});
$('.update_data').click(function(e){
	e.preventDefault();
	$.ajax({
		url: '{{ route('perencanaan.duplikasi_rencana') }}',
		type: 'post',
		data: $('#update_data').serialize(),
		success: function(response){
			if(response.sukses){
				$('#modal_content').modal('toggle');
				swal({title: response.title, text: response.text,icon: response.icon, closeOnClickOutside: false}).then((result) => {
					$('#datatable').DataTable().ajax.reload(null, false);
				});
			} else {
				swal({title: response.title, text: response.text,icon: response.icon, closeOnClickOutside: false});
			}
		}
	});
});
</script>
@endsection