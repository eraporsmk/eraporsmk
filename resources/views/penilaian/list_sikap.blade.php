@extends('adminlte::page')

@section('title_postfix', $title.' | ')

@section('content_header')
    <h1>{{$title}}</h1>
@stop
@section('content_header_right')
<a href="{{url('penilaian/sikap')}}" class="btn btn-success pull-right">Tambah Data</a>
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
				<th style="width: 20%">Nama PD</th>
				<th style="width: 10%">Rombel/Tingkat</th>
				<th style="width: 10%" class="text-center">Butir Sikap</th>
                <th style="width: 10%" class="text-center">Opsi Sikap</th>
                <th style="width: 35%" class="text-center">Uraian Sikap</th>
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
        "ajax": "{{ url('penilaian/get-list-sikap') }}",
		"columns": [
            { "data": "nama_siswa" },
            { "data": "nama_rombel" },
			{ "data": "get_butir_sikap" },
			{ "data": "get_opsi_sikap" },
            { "data": "uraian_sikap" },
        ]
    });
});
</script>
@Stop