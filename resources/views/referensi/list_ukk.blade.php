@extends('adminlte::page')

@section('title_postfix', 'Referensi Uji Kompetensi Keahlian (UKK) |')

@section('content_header')
    <h1>Referensi Uji Kompetensi Keahlian (UKK)</h1>
@stop
@section('content_header_right')
<a href="{{url('referensi/tambah-ukk')}}" class="btn btn-success pull-right">Tambah Data</a>
@stop
<?php
/*
@section('box-title')
	Judul
@stop
*/
?>
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
				<th width="20%">Kompetensi Keahlian</th>
				<th width="10%">Nomor Paket</th>
				<th width="50%">Nama Paket</th>
				<th width="5%" class="text-center">Jml Unit</th>
				<th width="5%" class="text-center">Status</th>
				<th width="10%" class="text-center">Aksi</th>
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
	$('a.confirm').bind('click',function(e) {
		var ini = $(this).parents('tr');
		e.preventDefault();
		var url = $(this).attr('href');
		var status = $(this).data('status');
		var text_tampil = (status) ? 'Referensi UKK ini tidak akan ditampilkan pada pilihan penilaian UKK!' : 'Referensi UKK ini akan ditampilkan pada pilihan penilaian UKK!';
		swal({
			title: "Apakah Anda yakin?",
			text: text_tampil,
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
        "ajax": "{{ url('referensi/list-ukk') }}",
		"columns": [
            { "data": "nama_jurusan" },
            { "data": "nomor_paket" },
			{ "data": "nama_paket_id" },
			{ "data": "jumlah_unit" },
			{ "data": "status" },
			{ "data": "tindakan" },
        ],
		"fnDrawCallback": function(oSettings){
			turn_on_icheck();
		}
    });
});
</script>
@Stop