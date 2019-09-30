@extends('adminlte::page')

@section('title_postfix', 'Prestasi Peserta Didik |')

@section('content_header')
    <h1>Prestasi Peserta Didik</h1>
@stop
@role('wali')
	@section('content_header_right')
	<a href="{{url('laporan/tambah-prestasi')}}" class="btn btn-success pull-right">Tambah Data</a>
	@stop
@endrole
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
				<th class="text-center">Nama Peserta Didik</th>
				<th class="text-center">Kelas</th>
				<th class="text-center">Jenis Prestasi</th>
                <th class="text-center">Keterangan</th>
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
}
$(document).ready( function () {
	var table = $('#datatable').DataTable( {
		"retrieve": true,
		"processing": true,
        "serverSide": true,
        "ajax": "{{ url('laporan/list-prestasi') }}",
		"columns": [
            { "name": "anggota_rombel_id", "data": "set_nama" },
			{ "data": "set_kelas" },
			{ "data": "jenis_prestasi" },
            { "data": "keterangan" },
        ],
		"fnDrawCallback": function(oSettings){
			turn_on_icheck();
		}
    });
});
</script>
@Stop