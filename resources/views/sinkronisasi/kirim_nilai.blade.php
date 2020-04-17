@extends('adminlte::page')

@section('content_header')
    <h1>Kirim Nilai ke Aplikasi Dapodik</h1>
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
	@if(!$status)
	<div class="callout callout-danger lead">
		Proses Pengiriman nilai tidak dapat dilakukan. <br />
		Tidak terhubung ke database Dapodik
	</div>
	@else
	<div class="callout callout-success lead">
		Proses pengiriman nilai dari eRaporSMK ke Dapodik, hanya bisa dilakukan di rombongan belajar yang terkunci status penilaiannya. Rekapitulasinya adalah sebagai berikut:
	</div>
	<div class="progress active" style="display:none;">
		<div class="progress-bar progress-bar-danger progress-bar-striped" role="progressbar" style="width: 0%;"></div>
	</div>
	<div class="status bg-black-active color-palette text-center" style="margin-bottom:10px; padding:10px 0px;display:none;">Memulai proses sinkronisasi</div>
	<table class="table table-bordered table-striped table-hover">
		<thead>
			<tr>
				<th class="text-center">No.</th>
				<th class="text-center">Tingkat</th>
				<th class="text-center">Jumlah Rombel</th>
				<th class="text-center">Jumlah Rombel Terkunci</th>
				<th class="text-center">Jumlah Rombel Belum Terkunci</th>
				<th class="text-center">Jumlah Nilai</th>
				<th class="text-center">Jumlah Nilai Terkirim</th>
				<th class="text-center">Aksi</th>
			</tr>
		</thead>
		<tbody>
		<?php
		$jumlah_jumlah = 0;
		$jumlah_terkunci = 0;
		$jumlah_terbuka = 0;
		$jumlah_nilai = 0;
		$jumlah_terkirim = 0;
		?>
		@foreach($rombongan_belajar as $rombel)
			<tr>
				<td class="text-center">{{$loop->iteration}}</td>
				<td class="text-center">{{$rombel['tingkat']}}</td>
				<td class="text-center">{{$rombel['jumlah']}}</td>
				<td class="text-center">{{$rombel['terkunci']}}</td>
				<td class="text-center">{{$rombel['terbuka']}}</td>
				<td class="text-center">{{$rombel['nilai']}}</td>
				<td class="text-center">{{$rombel['terkirim']}}</td>
				<td class="text-center">{!!($rombel['terkunci'] && $rombel['nilai']) ? '<a class="proses_kirim btn btn-success btn-sm" data-tingkat="'.$rombel['tingkat'].'" data-sekolah_id="'.$sekolah->sekolah_id.'" data-semester_id="'.$semester->semester_id.'">Kirim Nilai</a>' : '-' !!}</td>
			</tr>
			<?php
			$jumlah_jumlah += $rombel['jumlah'];
			$jumlah_terkunci += $rombel['terkunci'];
			$jumlah_terbuka += $rombel['terbuka'];
			$jumlah_nilai += $rombel['nilai'];
			$jumlah_terkirim +=$rombel['terkirim'];
			?>
		@endforeach
		</tbody>
		<tfoot>
			<tr>
				<th class="text-right" colspan="2">Jumlah</th>
				<th class="text-center">{{$jumlah_jumlah}}</th>
				<th class="text-center">{{$jumlah_terkunci}}</th>
				<th class="text-center">{{$jumlah_terbuka}}</th>
				<th class="text-center">{{$jumlah_nilai}}</th>
				<th class="text-center">{{$jumlah_terkirim}}</th>
				<th class="text-center">&nbsp;</th>
			</tr>
		</tfoot>
	</table>
	@endif
@Stop
@section('js')
<script>
var BarWidth = setInterval(frame, 1000);
var ProsesSinkronTable;
var Proses_Sinkron = function(m) {
	var url = '{{url('sinkronisasi/proses-kirim-nilai')}}/'+m.tingkat+'/'+m.sekolah_id+'/'+m.semester_id;
	$.get(url).done(function(response) {
		var data = $.parseJSON(response);
		console.log(data);
		if(data.status){
			$('.progress-bar').css('width',data.progress+'%');
			StatusText = $('.status').text();
			Proses_Sinkron(data);
			ProsesSinkronTable = data.rombel;
		} else {
			const wrapper = document.createElement('div');
			wrapper.innerHTML = data.message;
			clearInterval(BarWidth);
			$('.progress-bar').css('width','100%');
			$('.status').text('Proses sinkronisasi data selesai');
			swal({title: 'Selesai', icon: data.icon, content: wrapper, closeOnClickOutside: false}).then((result) => {
				window.location.replace('{{url('sinkronisasi/kirim-nilai/')}}');
			});
		}
	});
}
function Data_Sync(tingkat, sekolah_id, semester_id){
	this.tingkat = tingkat,
	this.sekolah_id = sekolah_id,
	this.semester_id = semester_id
}
function frame() {
	if(typeof ProsesSinkronTable != 'undefined'){
		$.ajax({
			url: "{{url('sinkronisasi/hitung-data/')}}/"+ProsesSinkronTable,
			success:function(response){
				if(response){
					var data = $.parseJSON(response);
					$('.status').text("Memproses kirim nilai ke Dapodik ("+data.jumlah+"/"+data.inserted+")");
				}
			}
		});
	}
}
$('.proses_kirim').click(function(){
	$('#spinner').remove();
	$('.progress').show();
	$('.status').show();
	var tingkat = $(this).data('tingkat');
	var sekolah_id = $(this).data('sekolah_id');
	var semester_id = $(this).data('semester_id');
	var DataSync = new Data_Sync(tingkat, sekolah_id, semester_id);
	Proses_Sinkron(DataSync);
});
</script>
@Stop