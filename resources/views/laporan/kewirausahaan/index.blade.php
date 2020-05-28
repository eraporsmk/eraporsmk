@extends('adminlte::page')

@section('title_postfix', 'Data Kewirausahaan Sekolah |')

@section('content_header')
    <h1>Data Kewirausahaan Sekolah</h1>
@stop
@role('wali')
	@section('content_header_right')
	<a href="{{route('laporan.tambah_kewirausahaan')}}" class="btn btn-success pull-right">Tambah Data</a>
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
				<th class="text-center">Pola Kewirausahaan</th>
				<th class="text-center">Jenis Kewirausahaan</th>
				<th class="text-center">Nama Produk</th>
				<th class="text-center">Aksi</th>
			</tr>
		</thead>
		<tbody>
		</tbody>
	</table>
<div id="modal_content" class="modal fade"></div>
@stop

@section('js')
<script type="text/javascript">
$(document).ready( function () {
	var table = $('#datatable').DataTable( {
		"retrieve": true,
		"processing": true,
        "serverSide": true,
        "ajax": "{{ url('laporan/list-kewirausahaan') }}",
		"columns": [
            { "name": "nama_siswa", "data": "nama_siswa" },
			{ "name": "jenis", "data": "jenis" },
			{ "name": "pola", "data": "pola" },
            { "name": "nama_produk", "data": "nama_produk" },
			{ "name": "actions", "data": "actions" },
        ],
		"fnDrawCallback": function(oSettings){
			turn_on_icheck();
		}
    });
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
				title: "Anda Yakin?",
				text: "Tindakan ini tidak bisa dikembalikan!",
				icon: "warning",
				buttons: true,
				dangerMode: true,
				closeOnClickOutside: false,
			}).then((willDelete) => {
				if (willDelete) {
					$.get(url).done(function(data) {
						swal({title: data.title, text: data.text,icon: data.icon, closeOnClickOutside: false}).then(function(){
							table.ajax.reload(null, null);
						});
					});
				}
			});
		});
	}
});
</script>
@Stop