@extends('adminlte::page')
@section('title_postfix', 'Perencanaan Penilaian UKK |')
@section('content_header')
    <h1>Perencanaan Penilaian UKK</h1>
@stop
@section('content_header_right')
<a href="{{url('perencanaan/tambah-ukk')}}" class="btn btn-success pull-right">Tambah Data</a>
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
				<th width="30%">Paket Kompetensi</th>
				<th width="25%">Internal</th>
				<th width="25%">Eksternal</th>
				<th width="10%" class="text-center">Aksi</th>
			</tr>
		</thead>
		<tbody>
		</tbody>
	</table>
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
						$('#datatable').DataTable().ajax.reload(null, false);
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
        "ajax": "{{ url('perencanaan/list-ukk') }}",
		"columns": [
            { "data": "paket_ukk" },
            { "data": "internal" },
			{ "data": "eksternal" },
			{ "data": "actions", "orderable": false },
        ],
		"fnDrawCallback": function(oSettings){
			turn_on_icheck();
		}
    });
});
</script>
@stop