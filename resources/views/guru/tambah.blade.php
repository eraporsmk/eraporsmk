@extends('adminlte::page')

@section('title_postfix', 'Tambah '.ucfirst($query).' | ')

@section('content_header')
    <h1>Tambah {{ucfirst($query)}}</h1>
@stop
@section('content')
	<div class="row">
	<div class="col-md-6">
		<a class="btn btn-success btn-lg btn-block" href="{{url('downloads/template-').$query}}">Unduh Format Excel {{ucfirst($query)}}</a>
	</div>
	<div class="col-md-6">
		<p class="text-center"><span class="btn btn-danger btn-file btn-lg btn-block"> Unggah Berkas Excel {{ucfirst($query)}}<input type="file" id="fileupload" name="file" /></span></p>
	</div>
</div>
@stop

@section('js')
<script src="{{ asset('vendor/blueimp/jquery-file-upload/js/vendor/jquery.ui.widget.js') }}"></script>
<script src="{{ asset('vendor/blueimp/jquery-file-upload/js/jquery.iframe-transport.js') }}"></script>
<script src="{{ asset('vendor/blueimp/jquery-file-upload/js/jquery.fileupload.js') }}"></script>
<script type="text/javascript">
$('.select2').select2();
	$.ajaxSetup({
		headers: {
			'X-CSRF-TOKEN': '{{ csrf_token() }}'
		}
	});
	$('#fileupload').fileupload({
		url: '{{url('/import-'.$query)}}',
		//dataType: 'json',
		progressall: function(e, data) {
			var progress = parseInt(data.loaded / data.total * 100, 10);
			$('#progress .progress-bar').css('width', progress + '%');
		},
		done: function(e, data) {
			var data = $.parseJSON(data.result);
			swal({title: data.title, text: data.text,icon: data.icon, closeOnClickOutside: false}).then((result) => {
				window.location.replace('{{url($query)}}');
			});
		}
	});
</script>
@Stop