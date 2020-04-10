@extends('adminlte::page')

@section('title_postfix', 'Data '.$title.' |')

@section('content_header')
    <h1>Data {{$title}}</h1>
@stop

<?php
/*
@section('box-title')
	Judul
@stop
*/
?>
@section('content')
	<div class="row" style="margin-bottom:10px;">
		<div class="col-md-4">
			<select id="filter_jurusan" class="form-control select2" style="width:100%;">
				<option value="">==Filter Berdasar Kompetensi Keahlian==</option>
				@if($all_jurusan->count())
				@foreach($all_jurusan as $jurusan)
				<option value="{{$jurusan->jurusan_sp_id}}">{{$jurusan->nama_jurusan_sp}}</option>
				@endforeach
				@endif
			</select>
		</div>
		<div id="filter_kelas_show" class="col-md-4" style="display:none;">
			<select id="filter_kelas" class="form-control select2" style="width:100%">
				<option value="">==Filter Berdasar Tingkat Kelas==</option>
				<option value="10">Kelas 10</option>
				<option value="11">Kelas 11</option>
				<option value="12">Kelas 12</option>
				<option value="13">Kelas 13</option>
			</select>
		</div>
		<div id="filter_rombel_show" class="col-md-4" style="display:none;">
			<select id="filter_rombel" class="form-control select2" style="width:100%">
				<option value="">==Filter Berdasar Rombel==</option>
			</select>
		</div>
	</div>
	<table id="datatable" class="table table-bordered table-striped table-hover">
		<thead>
			<tr>
				<th style="width: 40%">Nama</th>
                <th style="width: 3%" class="text-center">L/P</th>
                <th style="width: 20%">Tempat, Tanggal Lahir</th>
				<th style="width: 10%" class="text-center">Agama</th>
				@if($status == 'aktif')
                <th style="width: 10%" class="text-center">Rombel/Tingkat</th>
				@else
				<th style="width: 10%" class="text-center">Tanggal Dikeluarkan</th>
				@endif
                <th style="width: 10%" class="text-center">Tindakan</th>
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
	$('.select2').select2();
	var oTable = $('#datatable').DataTable( {
		"retrieve": true,
		"processing": true,
        "serverSide": true,
        "ajax": {
			"url": "{{ url('pd/list/'.$status) }}",
			"data": function (d) {
				var filter_jurusan = $('#filter_jurusan').val();
				var filter_kelas = $('#filter_kelas').val();
				var filter_rombel = $('#filter_rombel').val();
				if(filter_jurusan){
					d.filter_jurusan = filter_jurusan;
				}
				if(filter_kelas){
					d.filter_kelas = filter_kelas;
				}
				if(filter_rombel){
					d.filter_rombel = filter_rombel;
				}
			}
		},
		"columns": [
            { "data": "set_nama", "name": "nama" },
            { "data": "jenis_kelamin" },
			{ "data": "set_tempat_lahir", "name" : "tempat_lahir" },
			{ "data": "set_agama" },
            //{ "data": {{($status == 'aktif') ? '"rombel"' : '"tgl_keluar"'}}},
			@if($status == 'aktif')
			{ "data": "rombel" },
			@else
			{ "data": "tgl_keluar" },
			@endif
			{ "data": "actions", "orderable": false },
        ],
		"fnDrawCallback": function(oSettings){
			turn_on_icheck();
		}
    });
	$('#filter_jurusan').change(function(e){
		var ini = $(this).val();
		$('#filter_rombel_show').hide();
		if(ini == ''){
			$('#filter_kelas_show').hide();
		} else {
			$('#filter_kelas_show').show();
			$('#filter_kelas').prop("selectedIndex", 0);
			$("#filter_kelas").trigger('change.select2');
		}
        oTable.draw();
        e.preventDefault();
    });
	$('#filter_kelas').change(function(e){
        var ini = $(this).val();
		if(ini == ''){
			$('#filter_rombel_show').hide();
		} else {
			$('#filter_rombel_show').show();
			oTable.on( 'xhr', function () {
				$("#filter_rombel").html('<option value="">== Filter Berdasar Rombel ==</option>');
				var result = oTable.ajax.json();
				if(typeof result.data[0] !== 'undefined'){
					if(!$.isEmptyObject(result.data[0].rombongan_belajar.result)){
						$.each(result.data[0].rombongan_belajar.result, function (i, item) {
							$('#filter_rombel').append($('<option>', { 
								value: item.value,
								text : item.text
							}));
						});
					}
				}
			} );
		}
 		oTable.draw();
		e.preventDefault();
   });
	$('#filter_rombel').change(function(e){
		oTable.draw();
        e.preventDefault();
    });
});
</script>
@Stop