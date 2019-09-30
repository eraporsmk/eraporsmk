@extends('adminlte::page')

@section('content_header')
    <h1>Migrasi eRaporSMK v.4.x.x ke eRaporSMK v.5.0.0</h1>
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
	@if($status)
		@if($sekolah->sinkron)
			<div class="callout callout-danger lead">Migrasi eRaporSMK v.4.x.x ke eRaporSMK v.5.0.0 tidak dapat dilakukan karena sudah melakukan sinkronisasi dapodik!</div>
		@else
			<div class="callout callout-success lead">{{$output}}</div>
			<button type="button" class="btn btn-primary btn-lg btn-block" id="migrasi" data-loading-text="<i class='fa fa-spinner fa-spin '></i> Sedang Proses Migrasi"><i class="fa fa-refresh"></i> Proses Migrasi</button>
			<div class="status bg-black-active color-palette text-center" style="margin:10px 0px 10px 0px; padding:10px 0px; display:none;">Memulai proses sinkronisasi</div>
			<div class="progress active" style="display:none;">
				<div class="progress-bar progress-bar-warning progress-bar-striped" role="progressbar" style="width: 0%"></div>
			</div>
			<table class="table table-bordered table-striped table-hover" style="margin-top:10px;">
				<thead>
					<tr>
						<th width="5%" class="text-center">No</th>
						<th width="50%" class="text-center">Nama Tabel</th>
						<th width="15%" class="text-center">Jml Data</th>
						<th width="15%" class="text-center">Jml Masuk</th>
						<th width="15%" class="text-center">Keterangan</th>
					</tr>
				</thead>
					<tbody>
					@foreach($all_table as $table)
						<tr>
							<td class="text-center">{{$loop->iteration}}</td>
							<?php
							$jumlah_asal	= 0;
							$jumlah_masuk 	= 0;
							$query = App\Migrasi::where('nama_table', '=', $table['name'])->first();
							if($query){
								$jumlah_asal	= $query->jumlah_asal;
								$jumlah_masuk 	= $query->jumlah_masuk;
							} else {
								$jumlah_asal = $table['jumlah'];
							}
							?>
							<td>{{$table['name']}}</td>
							<td class="text-center">{{number_format($jumlah_asal, 0,',','.')}}</td>
							<td class="text-center">{{number_format($jumlah_masuk, 0,',','.')}}</td>
							<td class="text-center">{!!($jumlah_asal > $jumlah_masuk) ? '<span class="label label-danger">Kurang</span>' : '<span class="label label-success">Lengkap</span>' !!}</td>
						</tr>
					@endforeach
					</tbody>
			</table>
		@endif
	@else
		<div class="callout callout-danger lead">{{$output}}</div>
	@endif
@Stop
@section('js')
<script>
var BarWidth = setInterval(frame, 1000);
var ProsesSinkronTable;
var Proses_Sinkron = function(m) {
	var url = '{{url('sinkronisasi/proses-erapor4/')}}/'+m.table;
	$.get(url).done(function(response) {
		if(response){
			var data = $.parseJSON(response);
			if(data.status){
				console.log(data);
				$('.progress-bar').css('width',data.progress+'%');
				StatusText = $('.status').text();
				Proses_Sinkron(data);
				ProsesSinkronTable = 'migrasi';
			} else {
				clearInterval(BarWidth);
				$('.progress-bar').css('width','100%');
				$('.status').text('Proses migrasi data selesai');
				swal({title: 'Selesai', icon: 'success', closeOnClickOutside: false}).then((result) => {
					window.location.replace('{{url('sinkronisasi/erapor4')}}');
				});
			}
		}
	});
}
function Data_Sync(table){
	this.table = table;
}
function frame() {
	if(typeof ProsesSinkronTable != 'undefined'){
		$.ajax({
			url: "{{url('sinkronisasi/hitung-data/')}}/"+ProsesSinkronTable,
			success:function(response){
				if(response){
					var data = $.parseJSON(response);
					console.log(data);
					$('.progress-bar').css('width',data.progress+'%');
					$('.status').text("Proses migrasi data "+data.table+" ("+data.inserted+" / "+data.jumlah+")");
				}
			}
		});
	}
}
$('#migrasi').click(function(){
	var $this = $(this);
	$this.button('loading');
	$('#spinner').remove();
	$('.progress').show();
	$('.status').show();
	$('.progress-bar').css('width','2%');
	var DataSync = new Data_Sync("start_migrasi");
	Proses_Sinkron(DataSync);
});
</script>
@Stop