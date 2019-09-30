@extends('layouts.modal')

@section('title') Perbaharui nilai sikap @stop



@section('content')
<form method="post" id="update_data">
{{ csrf_field() }}
<div class="row">
	<div class="col-md-12">
		<div class="form-group" style="margin-bottom:10px;">
			<label for="butir_sikap_edit" class="col-sm-3 control-label">Butir Sikap</label>
			<div class="col-sm-4">
				<input type="hidden" name="nilai_sikap_id_edit" value="{{$nilai_sikap->nilai_sikap_id}}" />
				<select class="form-control" name="sikap_id_edit" required>
					<option value="">== Pilih Butir Sikap ==</option>
					@foreach($all_sikap as $ref_sikap)
					<option value="{{$ref_sikap->sikap_id}}"{{($nilai_sikap->sikap_id == $ref_sikap->sikap_id) ? ' selected="selected"' : ''}}>{{$ref_sikap->butir_sikap}}</option>
					@endforeach
				</select>
			</div>
			<div class="col-sm-4">
				<select name="opsi_sikap_edit" class="form-control" required>
					<option value="1"{{($nilai_sikap->opsi_sikap == 1) ? ' selected="selected"' : ''}}>Positif</option>
					<option value="2"{{($nilai_sikap->opsi_sikap == 2) ? ' selected="selected"' : ''}}>Negatif</option>
				</select>
			</div>
			<div style="clear:both;"></div>
		</div>
		<div class="form-group">
			<label for="uraian_sikap_edit" class="col-sm-3 control-label">Catatan Perilaku</label>
			<div class="col-sm-9">
				<textarea name="uraian_sikap_edit" class="form-control" required>{{$nilai_sikap->uraian_sikap}}</textarea>
			</div>
			<div style="clear:both;"></div>
		</div>
		<div style="margin-top:20px;"></div>
		<div class="col-sm-3"></div>
	</div>
</div>
</form>
@stop

@section('footer')
<a class="btn btn-primary" id="button_form" href="javascript:void(0);">Update</a>
<a class="btn btn-default" href="javascript:void(0)" data-dismiss="modal">Tutup</a>
@endsection

@section('js')
<script>
$('#button_form').click(function(){
	$.ajax({
		url: '{{url('penilaian/update-sikap')}}',
		type: 'post',
		data: $('#update_data').serialize(),
		success: function(response){
			var data = $.parseJSON(response);
			swal(data.title, {icon: data.icon,}).then((result) => {
				$('#modal_content').modal('hide');
				$.ajax({
					url: '{{url('/ajax/get-sikap')}}',
					type: 'post',
					data: $("form#form").serialize(),
					success: function(response){
						$('#simpan').hide();
						$('#result').html(response);
					}
				});
			});
		}
	});
});
</script>
@endsection