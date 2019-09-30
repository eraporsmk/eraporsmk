@extends('layouts.modal')

@section('title') Ubah nilai prestasi @stop



@section('content')
	<form id="update_data" class="form-horizontal">
		{{ csrf_field() }}
	<input type="hidden" name="prestasi_id" value="{{$prestasi->prestasi_id}}" />
	<div class="form-group">
		<label for="mitra_prakrein" class="col-sm-2 control-label">Jenis Prestasi</label>
		<div class="col-sm-5">
			<select name="jenis_prestasi" id="jenis_prestasi" class="select2 form-control" style="width:100%" required>
				<option value="">== Pilih Jenis Prestasi ==</option>
				<option value="Kurikuler"{{($prestasi->jenis_prestasi == 'Kurikuler') ? 'selected="selected"' : ''}}>Kurikuler</option>
				<option value="Ekstra Kurikuler"{{($prestasi->jenis_prestasi == 'Ekstra Kurikuler') ? 'selected="selected"' : ''}}>Ekstra Kurikuler</option>
				<option value="Catatan Khusus Lainnya"{{($prestasi->jenis_prestasi == 'Catatan Khusus Lainnya') ? 'selected="selected"' : ''}}>Catatan Khusus Lainnya</option>
			</select>
		</div>
	</div>
	<div class="form-group">
		<label for="lokasi_prakerin" class="col-sm-2 control-label">Keterangan Prestasi</label>
		<div class="col-sm-5">
			<input type="text" name="keterangan_prestasi" id="keterangan_prestasi" class="form-control" value="{{$prestasi->keterangan_prestasi}}" required />			
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
		url: '{{ route('laporan.update_prestasi') }}',
		type: 'post',
		data: $('#update_data').serialize(),
		success: function(response){
			var data = $.parseJSON(response);
			if(data.sukses){
				$('#modal_content').modal('toggle');
				swal({title: data.title, text: data.text,icon: data.icon, closeOnClickOutside: false}).then(function(){
					$.ajax({
						url: '{{url('ajax/get-prestasi')}}',
						type: 'post',
						data: $('#form').serialize(),
						success: function(response){
							$('.simpan').show();
							$('#result').html(response);
						}
					});
				});
			}
		}
	});
});
</script>
@Stop