@extends('adminlte::page')

@section('title_postfix', 'Referensi Ekstrakurikuler |')

@section('content_header')
    <h1>Referensi Ekstrakurikuler</h1>
@stop

<?php
/*
@section('box-title')
	Judul
@stop
*/
?>
@section('content')
	<table id="datatable" class="table table-bordered table-striped table-hover">
		<thead>
			<tr>
				<th style="width: 15%">Nama Ekstrakurikuler</th>
                <th style="width: 15%">Nama Pembina</th>
				<th style="width: 15%">Prasarana</th>
				<th style="width: 10%" class="text-center">Anggota Ekskul</th>
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
	$('a.sync_anggota').bind('click',function(e) {
		e.preventDefault();
		var url = $(this).attr('href');
		$.get(url, function(response) {
			var data = $.parseJSON(response);
			swal(data.message, {icon: data.icon});
		});
	});
}
$(document).ready( function () {
	var table = $('#datatable').DataTable( {
		"retrieve": true,
		"processing": true,
        "serverSide": true,
        "ajax": "{{ url('referensi/list-ekskul') }}",
		"columns": [
            { "data": "nama_ekskul" },
            { "data": "guru.nama" },
			{ "data": "alamat_ekskul" },
			{ "data": "anggota" },
			{ "data": "sync_anggota" },
        ],
		"fnDrawCallback": function(oSettings){
			turn_on_icheck();
		}
    });
});
</script>
@Stop