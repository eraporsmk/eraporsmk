@extends('adminlte::page')

@section('title_postfix', 'Referensi Mata Pelajaran |')

@section('content_header')
    <h1>Referensi Mata Pelajaran</h1>
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
				<th style="width: 25%">Kurikulum</th>
				<th style="width: 25%">Mata Pelajaran</th>
				<th style="width: 10%">Tingkat Pendidikan</th>
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
        "ajax": "{{ url('referensi/list-mata-pelajaran') }}",
		"columns": [
            { "data": "kurikulum.nama_kurikulum" },
            { "data": "mata_pelajaran.nama" },
			{ "data": "tingkat_pendidikan_id" },
        ],
		"fnDrawCallback": function(oSettings){
			turn_on_icheck();
		}
    });
});
</script>
@Stop