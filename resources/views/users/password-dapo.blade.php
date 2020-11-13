@extends('layouts.modal')

@section('title') Perbaharui Password Dapodik @stop
@section('content')
<div class="callout callout-danger lead" id="text-error" style="display: none;">
	<span class="text-error"></span>
</div>
<form id="password_post" method="post">
	@csrf
	<div class="form-group">
		<label for="password">Password Lama Dapodik</label>
		<input type="text" class="form-control" name="password">
	</div>
	<div class="form-group">
		<label for="password_confirmation">Konfirmasi Password Lama Dapodik</label>
		<input type="text" class="form-control" name="password_confirmation">
	</div>
</form>
@stop
@section('footer')
<a class="btn btn-default btn-sm" href="javascript:void(0)" data-dismiss="modal">Tutup</a>
<a href="javascript:void(0)" class="btn btn-success btn-sm simpan_password"><i class="fa fa-plus-circle"></i> Simpan</a>
@endsection
@section('js')
<script>
	$('a.simpan_password').click(function(){
		var post_data = $("form#password_post").serialize();
		console.log(post_data);
		$.ajax({
			url: '{{route('atur_password_dapodik')}}',
			type: 'post',
			data: post_data,
			success: function(response){
				console.log(response);
				$('#modal_content').modal('toggle');
				//return false;
				swal({title: response.title, icon: response.icon, text: response.text, closeOnClickOutside: false}).then((result) => {
					window.location.replace('{{url('sinkronisasi/dapodik/')}}');
				});
			},
			error: function(XMLHttpRequest, textStatus, errorThrown) {
				var error = [];
				$.each(XMLHttpRequest.responseJSON.errors, function(i, v){
					error.push(v[0]);
				})
				$('#text-error').show();
				$('.text-error').html(error.join('<br>'));
				console.log(error);
			}
		});
	});
</script>
@endsection