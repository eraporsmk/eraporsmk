@extends('adminlte::page')

@section('title', 'eRaporSMK')

@section('content_header')
    <h1>Data Rombongan Belajar</h1>
@stop

<?php
/*
@section('box-title')
	Judul
@stop
*/
?>
@section('content')
<div class="table-responsive">
	<table id="datatable" class="table table-bordered table-striped table-hover">
		<thead>
			<tr>
				<th style="width: 8%">Nama Rombel</th>
                <th style="width: 10%">Wali Kelas</th>
                <th style="width: 5%">Tingkat</th>
				<th style="width: 10%" class="text-center">Program/Kompetensi Keahlian</th>
				<th style="width: 10%" class="text-center">Kurikulum</th>
                <th style="width: 10%" class="text-center">Anggota Rombel</th>
                <th style="width: 5%" class="text-center">Pembelajaran</th>
			</tr>
		</thead>
		<tbody>
		</tbody>
	</table>
</div>
<div id="modal_content" class="modal fade"></div>
@stop

@section('js')
<!--script src="{{ asset('vendor/adminlte/plugins/jquery-noty/packaged/jquery.noty.packaged.js') }}"></script>
<script src="{{ asset('vendor/adminlte/plugins/jquery-tabledit/jquery.tabledit.js') }}"></script-->
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
        "ajax": "{{ url('rombel/list-rombel') }}",
		"columns": [
            { "data": "nama" },
            { "data": "wali" },
			{ "data": "tingkat" },
			{ "data": "jurusan" },
            { "data": "kurikulum" },
			{ "data": "anggota", "orderable": false },
			{ "data": "pembelajaran", "orderable": false },
        ],
		"fnDrawCallback": function(oSettings){
			turn_on_icheck();
		}
    });
});
</script>
@Stop