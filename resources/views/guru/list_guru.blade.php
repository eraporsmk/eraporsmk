@extends('adminlte::page')

@section('title_postfix', 'Data '.ucfirst($query).' | ')

@section('content_header')
    <h1>Data {{$title}}</h1>
@stop
@if($query == 'instruktur' || $query == 'asesor')
	@section('content_header_right')
		<a href="{{url('tambah-'.$query)}}" class="btn btn-success pull-right">Tambah Data</a>
	@stop
@endif
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
				<th>Nama</th>
                <th class="text-center">L/P</th>
                <th>Tempat, Tanggal Lahir</th>
                <th class="text-center">Email</th>
                <th class="text-center">Tindakan</th>
			</tr>
		</thead>
		<tbody>
		</tbody>
	</table>
<div id="modal_content" class="modal fade"></div>
@stop

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
	var table = $('#datatable').DataTable( {
		"retrieve": true,
		"processing": true,
        "serverSide": true,
        "ajax": "{{ url('guru/list-guru/'.$query) }}",
		"columns": [
            { "name": "nama", "data": "set_nama" },
            { "data": "jenis_kelamin" },
			{ "data": "set_tempat_lahir" },
            { "data": "email" },
			{ "data": "actions", "orderable": false },
        ],
		"fnDrawCallback": function(oSettings){
			turn_on_icheck();
		}
    });
});
</script>
@Stop