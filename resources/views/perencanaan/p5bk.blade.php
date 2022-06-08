@extends('adminlte::page')
@section('title_postfix', 'Perencanaan Penilaian P5BK |')
@section('content_header')
    <h1>Perencanaan Penilaian P5BK</h1>
@stop
@section('content_header_right')
<a href="{{url('perencanaan/tambah-p5bk')}}" class="btn btn-success pull-right">Tambah Data</a>
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
			<select id="filter_jurusan" class="form-control select2" style="width:100%">
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
				<option value="">==Filter Berdasar Tingkat==</option>
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
				<th style="vertical-align:middle" width="10%">Kelas</th>
				<th style="vertical-align:middle" width="10%">Nama Projek</th>
				<th class="text-center" style="vertical-align:middle" width="10%">Deskripsi</th>
				<th class="text-center" width="5%">Jumlah <br />Aspek Dinilai</th>
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
	$('a.confirm').bind('click',function(e) {
		e.preventDefault();
		var url = $(this).attr('href');
		swal({
			title: "Anda Yakin?",
			text: "Semua nilai dibawah perencanaan terpilih akan terhapus!",
			icon: "warning",
			buttons: true,
			dangerMode: true,
			closeOnClickOutside: false,
		}).then((willDelete) => {
			if (willDelete) {
				$.get(url).done(function(data) {
					swal({title: data.title, text: data.text,icon: data.icon, closeOnClickOutside: false}).then(results => {
							window.location.replace('{{route('perencanaan.p5bk')}}');
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
			"url": "{{ url('perencanaan/list-rencana-p5bk') }}",
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
            { "data": "rombongan_belajar", "name": "rombongan_belajar.nama", "orderable": false },
            { "data": "nama", "name": "nama" },
			{ "data": "deskripsi" },
			{ 
				"data": "aspek_budaya_kerja_count",
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