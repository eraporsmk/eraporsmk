@extends('layouts.modal')

@section('title'){{ $title }} @stop



@section('content')
	<?php print_r($guru); ?>
@stop

@section('footer')
    <a class="btn btn-default" href="javascript:void(0)" data-dismiss="modal">Close</button>
@endsection

@section('js')
<script type="text/javascript">
var hari = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
var bulan = ['Januari', 'Februari', 'Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'];
function turn_on_icheck(){
	$('a.toggle-modal').bind('click',function(e) {
		e.preventDefault();
		var url = $(this).attr('href');
		if (url.indexOf('#') == 0) {
			$('#modal_content').modal('open');
	        $('.editor').wysihtml5();
		} else {
			$.get(url, function(data) {
				$('#modal_content').modal();
				$('#modal_content').html(data);
			});
		}
	});
}
$(document).ready( function () {
	var t = $('#datatable').on('draw.dt', function () {
		turn_on_icheck();
	});
	$('#datatable').on( 'processing.dt', function ( e, settings, processing ) {
		$('#spinner').show();
	});
	$('#datatable').DataTable( {
        "processing": true,
        "serverSide": true,
        "ajax": "{{ url('guru/list-guru') }}",
		"columns": [
            { "data": "set_nama" },
            { "data": "jenis_kelamin" },
			{ "data": "set_tempat_lahir" },
            { "data": "email" },
            { "data": "actions", "orderable": false},
        ]
    } );
});
</script>
@Stop