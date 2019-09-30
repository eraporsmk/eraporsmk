@extends('adminlte::page')

@section('title_postfix', 'Referensi Teknik Penilaian |')

@section('content_header')
    <h1>Referensi Teknik Penilaian</h1>
@stop

@section('content_header_right')
	<a href="{{url('referensi/tambah-metode')}}" class="btn btn-success pull-right">Tambah Data</a>
@stop

@section('content')
	@if ($message = Session::get('success'))
      <div class="alert alert-success alert-block alert-dismissable"><i class="fa fa-check"></i>
        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
          <strong>Sukses!</strong> {{ $message }}
      </div>
    @endif

    @if ($message = Session::get('error'))
      <div class="alert alert-danger alert-block alert-dismissable"><i class="fa fa-ban"></i>
        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
        <strong>Error!</strong> {{ $message }}
      </div>
    @endif
	<table id="datatable" class="table table-bordered table-striped table-hover">
		<thead>
			<tr>
				<th style="width: 10%">Kompetensi Penilaian</th>
				<th style="width: 20%">Nama Metode</th>
				<th style="width: 8%" class="text-center">Tindakan</th>
			</tr>
		</thead>
		<tbody>
		</tbody>
	</table>
<div id="modal_content" class="modal fade"></div>
@stop

@section('js')
<script type="text/javascript">
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
	$('a.confirm').bind('click',function(e) {
		var ini = $(this).parents('tr');
		e.preventDefault();
		var url = $(this).attr('href');
		swal({
			title: "Apakah Anda yakin?",
			text: "Tindakan ini tidak dapat dikembalikan!",
			icon: "warning",
			buttons: true,
			dangerMode: true,
			closeOnClickOutside: false,
		}).then((willDelete) => {
			if (willDelete) {
				$.get(url).done(function(response) {
					var data = $.parseJSON(response);
					swal(data.text, {icon: data.icon,}).then((result) => {
						window.location.replace('{{url('referensi/metode')}}');
					});
				});
			}
		});
	});
}
$(document).ready( function () {
	var table = $('#datatable').DataTable( {
		"retrieve": true,
		"processing": true,
        "serverSide": true,
        "ajax": "{{ url('referensi/list-metode') }}",
		"columns": [
            { "data": "kompetensi" },
            { "data": "nama" },
			{ "data": "tindakan" },
        ],
		"fnDrawCallback": function(oSettings){
			turn_on_icheck();
		}
    });
});
</script>
@Stop