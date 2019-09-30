@extends('layouts.modal')

@section('title') Edit Paket UKK @stop
@section('content')
<form class="form-horizontal" id="update_data" method="post" accept-charset="utf-8">
	{{ csrf_field() }}
	<input type="hidden" class="form-control" name="paket_ukk_id" value="{{$paket_ukk->paket_ukk_id}}" />
	<div class="form-group">
		<label for="jurusan" class="col-sm-2 control-label">Kompetensi Keahlian</label>
		<div class="col-sm-5">
			<input type="text" class="form-control" readonly="" value="{{$paket_ukk->jurusan->nama_jurusan}}" />
		</div>
	</div>
	<div class="form-group">
		<label for="jurusan" class="col-sm-2 control-label">Kurikulum</label>
		<div class="col-sm-5">
			<input type="text" class="form-control" readonly="" value="{{$paket_ukk->kurikulum->nama_kurikulum}}" />
		</div>
	</div>
	<div class="form-group">
		<label for="jurusan" class="col-sm-2 control-label">Nomor Paket</label>
		<div class="col-sm-5">
			<input type="text" class="form-control" readonly="" value="{{$paket_ukk->nomor_paket}}" />
		</div>
	</div>
	<div class="form-group">
		<label for="jurusan" class="col-sm-2 control-label">Judul Paket (ID)</label>
		<div class="col-sm-5">
			<input type="text" class="form-control" name="nama_paket_id" value="{{$paket_ukk->nama_paket_id}}" />
		</div>
	</div>
	<div class="form-group">
		<label for="jurusan" class="col-sm-2 control-label">Judul Paket (EN)</label>
		<div class="col-sm-5">
			<input type="text" class="form-control" name="nama_paket_en" value="{{$paket_ukk->nama_paket_en}}" />
		</div>
	</div>
	<div class="form-group">
		<label for="jurusan" class="col-sm-2 control-label">Status</label>
		<div class="col-sm-5">
			<select name="status" class="select2 form-control" style="width:100%">
				<option value="1"{{($paket_ukk->status) ? ' selected="selected"' : ''}}>Aktif</option>
				<option value="0"{{($paket_ukk->status) ? '' : ' selected="selected"'}}>Non Aktif</option>
			</select>
		</div>
	</div>
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
		url: '{{ route('referensi.update_ukk') }}',
		type: 'post',
		data: $('#update_data').serialize(),
		success: function(response){
			var data = $.parseJSON(response);
			if(data.sukses){
				$('#modal_content').modal('toggle');
				swal({title: data.title, text: data.text,icon: data.icon, closeOnClickOutside: false}).then((result) => {
					$('#datatable').DataTable().ajax.reload(null, false);
				});
			}
		}
	});
});
</script>
@Stop