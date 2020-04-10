@extends('adminlte::page')

@section('title_postfix', 'Referensi Kompetensi Dasar |')

@section('content_header')
    <h1>Referensi Kompetensi Dasar</h1>
@stop

@section('content_header_right')
    <a href="{{url('referensi/add-kd')}}" class="btn btn-success pull-right">Tambah Data</a>
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
		<input type="hidden" id="filter_tingkat" value="0" />
		<input type="hidden" id="filter_jurusan" value="0" />
		<div class="col-md-4">
			<select id="filter_mapel" class="form-control select2" style="width:100%;">
				<option value="">==Filter Berdasar Mata Pelajaran==</option>
				@if($all_pembelajaran->count())
				@foreach($all_pembelajaran as $pembelajaran)
				<option value="{{$pembelajaran->mata_pelajaran_id}}">{{$pembelajaran->nama_mata_pelajaran}}</option>
				@endforeach
				@endif
			</select>
		</div>
		<div id="filter_kelas_show" class="col-md-4" style="display:none;">
			<select id="filter_kelas" class="form-control select2" style="display:none;width:100%">
				<option value="">==Filter Berdasar Tingkat Kelas==</option>
				<option value="10">Kelas 10</option>
				<option value="11">Kelas 11</option>
				<option value="12">Kelas 12</option>
				<option value="13">Kelas 13</option>
			</select>
		</div>
		<div id="filter_kompetensi_show" class="col-md-4" style="display:none;">
			<select id="filter_kompetensi" class="form-control select2" style="display:none;width:100%">
				<option value="">==Filter Berdasar Kompetensi==</option>
				<option value="1">Pengetahuan</option>
				<option value="2">Keterampilan</option>
			</select>
		</div>
	</div>
	<table id="datatable" class="table table-bordered table-striped table-hover">
		<thead>
			<tr>
				<th style="width: 15%">Mata Pelajaran</th>
				<th style="width: 5%" class="text-center">Kode</th>
                <th style="width: 5%" class="text-center">Kelas</th>
				<th style="width: 60%">Isi Kompetensi</th>
                <th style="width: 5%">Kurikulum</th>
				<th style="width: 5%" colspan="text-center">Status</th>
                <th style="width: 5%" class="text-center">Tindakan</th>
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
	$('.tooltip-left').tooltip({
		placement: 'left',
		viewport: {selector: 'body', padding: 5}
	});
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
			title: "Apakah Anda yakin?",
			text: "Tindakan ini tidak dapat dikembalikan!",
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
	$('a.confirm_aktif').bind('click',function(e) {
		var ini = $(this).closest('td').prev('td').find('.btn-xs');
		e.preventDefault();
		var url = $(this).attr('href');
		var status = $(this).data('status');
		var text_tampil = (status) ? 'KD tidak ditampilkan di perencanaan' : 'KD akan ditampilkan di perencanaan';
		console.log(text_tampil);
		swal({
			title: "Anda Yakin?",
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
	$('.select2').select2();
	var oTable = $('#datatable').DataTable( {
		"retrieve": true,
		"processing": true,
        "serverSide": true,
        "ajax": {
			"url": "{{ url('referensi/list-kd') }}",
			"data": function (d) {
				var mata_pelajaran_id = $('#filter_mapel').val();
				var filter_kelas = $('#filter_kelas').val();
				var filter_kompetensi = $('#filter_kompetensi').val();
				if(mata_pelajaran_id){
					d.mata_pelajaran_id = mata_pelajaran_id;
				}
				if(filter_kelas){
					d.filter_kelas = filter_kelas;
				}
				if(filter_kompetensi){
					d.filter_kompetensi = filter_kompetensi;
				}
			}
		},
		"columns": [
            { "data": "mata_pelajaran.nama" },
            { "data": "id_kompetensi" },
			{ "data": "kelas" },
			{ "data": "isi_kd" },
			{ "data": "kurikulum" },
			{ "data": "status" },
			{ "data": "tindakan" },
        ],
		"fnDrawCallback": function(oSettings){
			turn_on_icheck();
		}
    });
	$('#filter_mapel').change(function(e){
		var ini = $(this).val();
		$('filter_kompetensi_show').hide();
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
			$('#filter_kompetensi_show').hide();
		} else {
			$('#filter_kompetensi_show').show();
			$('#filter_kompetensi_show').prop("selectedIndex", 0);
			$("#filter_kompetensi_show").trigger('change.select2');
		}
        oTable.draw();
        e.preventDefault();
    });
	$('#filter_kompetensi').change(function(e){
		oTable.draw();
        e.preventDefault();
    });
});
</script>
@Stop