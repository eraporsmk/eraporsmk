@extends('adminlte::page')
@section('title_postfix', 'Perencanaan Penilaian Keterampilan |')
@section('content_header')
    <h1>Perencanaan Penilaian Keterampilan</h1>
@stop
@section('content_header_right')
<a href="{{url('perencanaan/tambah-keterampilan')}}" class="btn btn-success pull-right">Tambah Data</a>
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
    <div class="row" style="margin-bottom:10px;">
		<div class="col-md-4">
			<select id="filter_jurusan" class="form-control select2">
				<option value="">==Filter Berdasar Kompetensi Keahlian==</option>
				<option value="15052520">Multimedia</option>
				<option value="35088750">Administrasi Perkantoran</option>
				<option value="35090755">Akuntansi</option>
				<option value="35281530">Otomatisasi dan Tata Kelola Perkantoran</option>
				<option value="15052">Teknik Komputer dan Informatika</option>
				<option value="35281">Manajemen Perkantoran</option>
				<option value="35291">Akuntansi dan Keuangan</option>
				<option value="35291535">Akuntansi dan Keuangan Lembaga</option>
			</select>
		</div>
		<div class="col-md-4">
			<select id="filter_tingkat" class="form-control" style="display:none;">
				<option value="">==Filter Berdasar Tingkat==</option>
				<option value="10">Kelas 10</option>
				<option value="11">Kelas 11</option>
				<option value="12">Kelas 12</option>
				<option value="13">Kelas 13</option>
			</select>
		</div>
		<div class="col-md-4">
			<select id="filter_rombel" class="form-control" style="display:none;"></select>
		</div>
	</div>
	<table id="datatable" class="table table-bordered table-striped table-hover">
		<thead>
			<tr>
				<th style="vertical-align:middle" width="30%">Mata Pelajaran</th>
				<th style="vertical-align:middle" width="10%">Kelas</th>
				<th style="vertical-align:middle" width="10%">Aktifitas Penilaian</th>
				<th class="text-center" style="vertical-align:middle" width="8%">Teknik Penilaian</th>
				<th class="text-center" style="vertical-align:middle" width="10%">Bobot</th>
				<th class="text-center" width="5%">Jumlah <br />KD</th>
				<th  style="vertical-align:middle;width: 5%" class="text-center">Tindakan</th>
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
}
$(document).ready( function () {
	var table = $('#datatable').DataTable( {
		"retrieve": true,
		"processing": true,
        "serverSide": true,
        "ajax": "{{ url('perencanaan/list-rencana/2') }}",
		"columns": [
            { "data": "nama_mata_pelajaran", "name": "pembelajaran.nama_mata_pelajaran" },
            //{ "data": "kelas" },
			{ "data": "pembelajaran.rombongan_belajar.nama", "name": "pembelajaran.rombongan_belajar.nama" },
			{ "data": "nama_penilaian" },
			{ "data": "metode", "name": "teknik_penilaian.nama" },
            { 
				"data": "bobot",
				"render": function ( data, type, row, meta ) {
					return '<div class="text-center">'+data+'</div>';
				}
			},
			{ 
				"data": "kd_nilai_count",
				"searchable": false,
				"render": function ( data, type, row, meta ) {
					return '<div class="text-center">'+data+'</div>';
				}
			},
			{ "data": "actions", "orderable": false },
        ],
		"fnDrawCallback": function(oSettings){
			turn_on_icheck();
		}
    });
});
</script>
@Stop