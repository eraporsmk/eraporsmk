@extends('adminlte::page')

@section('content_header')
<h1>Sinkronisasi Dapodik</h1>
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
@if ($dapodik)
@if (!$dapodik->error)
<div class="progress active" style="display:none;">
	<div class="progress-bar progress-bar-warning progress-bar-striped" role="progressbar" style="width: 0%"></div>
</div>
<div class="status bg-black-active color-palette text-center"
	style="margin-bottom:10px; padding:10px 0px; display:none;">Memulai proses sinkronisasi</div>
<a class="btn btn-lg btn-block btn-success" href="javascript:void(0)" id="sinkron">Proses Sinkronisasi</a>
<table class="table table-bordered table-striped table-hover">
	<thead>
		<tr>
			<th class="text-center">Data</th>
			<th class="text-center">Jml Data Dapodik</th>
			<th class="text-center">Jml Data Erapor</th>
			<th class="text-center">Jml Data Sudah Tersinkronisasi</th>
			<th class="text-center">Status</th>
		</tr>
	</thead>
	<tbody>
		<?php
			//dd($data);
			$make_array = array(
				0 => 
					array(
						'nama' => 'Sekolah',
						'link' => 'sekolah',
						'get_dapodik' => 1,
						'get_erapor' => $erapor->sekolah_erapor,
						'get_sinkron' => $erapor->sekolah_sinkron,
						'server' => 'erapor_server',
						'data' => 'sekolah',
						'aksi' => 'sekolah',
					), 
				1 => 
					array(
						'nama' => 'PTK',
						'link' => 'guru',
						'get_dapodik' => $dapodik->dapodik->ptk_terdaftar,
						'get_erapor' => $erapor->guru_erapor,
						'get_sinkron' => $erapor->guru_sinkron,
						'server' => 'erapor_server',
						'data' => 'guru',
						'aksi' => 'ptk',
					), 
				2 => 
					array(
						'nama' => 'Rombongan Belajar',
						'link' => 'rombongan-belajar',
						'get_dapodik' => $dapodik->dapodik->rombongan_belajar,
						'get_erapor' => $erapor->rombel_erapor,
						'get_sinkron' => $erapor->rombel_sinkron,
						'server' => 'erapor_server',
						'data' => 'rombongan_belajar',
						'aksi' => 'rombongan-belajar',
					), 
				3 => 
					array(
						'nama' => 'Peserta Didik Aktif',
						'link' => 'siswa-aktif',
						'get_dapodik' => $dapodik->dapodik->registrasi_peserta_didik,
						'get_erapor' => $erapor->siswa_erapor,
						'get_sinkron' => $erapor->siswa_sinkron,
						'server' => 'erapor_server',
						'data' => 'siswa_aktif',
						'aksi' => 'peserta-didik-aktif',
					), 
				4 => 
					array(
						'nama' => 'Peserta Didik Keluar',
						'link' => 'siswa-keluar',
						'get_dapodik' => $dapodik->dapodik->siswa_keluar_dapodik,
						'get_erapor' => $erapor->siswa_keluar_erapor,
						'get_sinkron' => $erapor->siswa_keluar_sinkron,
						'server' => 'erapor_server',
						'data' => 'siswa_keluar',
						'aksi' => 'peserta-didik-keluar',
					), 
				5 => 
					array(
						'nama' => 'Pembelajaran',
						'link' => 'pembelajaran',
						'get_dapodik' => $dapodik->dapodik->pembelajaran_dapodik,
						'get_erapor' => $erapor->pembelajaran_erapor,
						'get_sinkron' => $erapor->pembelajaran_sinkron,
						'server' => 'erapor_server',
						'data' => 'pembelajaran',
						'aksi' => 'pembelajaran',
					),
				6 => 
					array(
						'nama' => 'Ekstrakurikuler',
						'link' => 'ekskul',
						'get_dapodik' => $dapodik->dapodik->ekskul_dapodik,
						'get_erapor' => $erapor->ekskul_erapor,
						'get_sinkron' => $erapor->ekskul_sinkron,
						'server' => 'erapor_server',
						'data' => 'ekskul',
						'aksi' => 'ekstrakurikuler',
					),
				7 => 
					array(
						'nama' => 'Anggota Ekstrakurikuler',
						'link' => 'anggota-ekskul',
						'get_dapodik' => $dapodik->dapodik->anggota_ekskul_dapodik,
						'get_erapor' => $erapor->anggota_ekskul_erapor,
						'get_sinkron' => $erapor->anggota_ekskul_sinkron,
						'server' => 'erapor_server',
						'data' => 'anggota_ekskul',
						'aksi' => 'anggota-ekskul',
					),
				8 => 
					array(
						'nama' => 'Relasi Dunia Usaha &amp; Industri',
						'link' => 'dudi',
						'get_dapodik' => $dapodik->dapodik->dudi_dapodik,
						'get_erapor' => $erapor->dudi_erapor,
						'get_sinkron' => $erapor->dudi_sinkron,
						'server' => 'erapor_server',
						'data' => 'dudi',
						'aksi' => 'dudi',
					),
				9 => 
					array(
						'nama' => 'Jurusan',
						'link' => 'jurusan',
						'get_dapodik' => $dapodik->dapodik->jurusan,
						'get_erapor' => $erapor->jurusan_erapor,
						'get_sinkron' => $erapor->jurusan_sinkron,
						'server' => 'erapor_server',
						'data' => 'jurusan',
						'aksi' => 'jurusan',
					),
				10 => 
					array(
						'nama' => 'Kurikulum',
						'link' => 'kurikulum',
						'get_dapodik' => $dapodik->dapodik->kurikulum,
						'get_erapor' => $erapor->kurikulum_erapor,
						'get_sinkron' => $erapor->kurikulum_sinkron,
						'server' => 'erapor_server',
						'data' => 'kurikulum',
						'aksi' => 'kurikulum',
					),
				11 => 
					array(
						'nama' => 'Mata Pelajaran',
						'link' => 'mata-pelajaran',
						'get_dapodik' => $dapodik->dapodik->mata_pelajaran,
						'get_erapor' => $erapor->mata_pelajaran_erapor,
						'get_sinkron' => $erapor->mata_pelajaran_sinkron,
						'server' => 'erapor_server',
						'data' => 'mata_pelajaran',
						'aksi' => 'mata-pelajaran',
					),
				12 => 
					array(
						'nama' => 'Mata Pelajaran Kurikulum',
						'link' => 'mapel-kur',
						'get_dapodik' => $dapodik->dapodik->mata_pelajaran_kurikulum,
						'get_erapor' => $erapor->mata_pelajaran_kurikulum_erapor,
						'get_sinkron' => $erapor->mata_pelajaran_kurikulum_sinkron,
						'server' => 'erapor_server',
						'data' => 'mapel_kur',
						'aksi' => 'mata-pelajaran-kurikulum',
					),
				13 => 
					array(
						'nama' => 'Wilayah',
						'link' => 'wilayah',
						'get_dapodik' => $dapodik->wilayah,
						'get_erapor' => $erapor->wilayah_erapor,
						'get_sinkron' => $erapor->wilayah_sinkron,
						'server' => 'erapor_dashboard',
						'data' => 'wilayah',
						'aksi' => 'wilayah',
					),
				14 => 
					array(
						'nama' => 'Ref. Kompetensi Dasar',
						'link' => 'ref-kd',
						'get_dapodik' => $dapodik->ref_kd,
						'get_erapor' => $erapor->kompetensi_dasar_erapor,
						'get_sinkron' => $erapor->kompetensi_dasar_sinkron,
						'server' => 'erapor_dashboard',
						'data' => 'kompetensi_dasar',
						'aksi' => 'count_kd',
					),
			);
			foreach($make_array as $d){
				if($d['get_sinkron']){
					$status = 'Lengkap';
					$label = 'green';
					$btn = 'btn-danger';
					$text = 'Sinkron Ulang';
					if($d['get_dapodik'] > $d['get_sinkron']){
						$status = 'Kurang';
						$label = 'yellow';
						$btn = 'btn-warning';
						$text = 'Sinkron Ulang';
					}
					if($d['link'] == 'sekolah'){
						if($erapor->get_sekolah_sinkron){
							if(!$erapor->get_sekolah_sinkron->sinkron){
								$status = 'Sinkron Ulang';
								$label = 'yellow';
								$btn = 'btn-warning';
								$text = 'Sinkron Ulang';
							}
						}
					}
				} else {
					$status = 'Belum';
					$label = 'red';
					$btn = 'btn-success';
					$text = 'Sinkron';
				}
				if($d['link'] == 'mata_pelajaran' || $d['link'] == 'jurusan'){
					$id_sekolah_dapodik = '';
				}
			?>
		<tr>
			<td><?php echo $d['nama']; ?></td>
			<td class="text-center"><?php echo $d['get_dapodik']; ?></td>
			<td class="text-center"><?php echo $d['get_erapor']; ?></td>
			<td class="text-center"><?php echo $d['get_sinkron']; ?></td>
			<td><small class="label bg-<?php echo $label; ?>"><?php echo $status; ?></small></td>
			<td class="text-center"><a href="javascript:void(0)" data-server="<?php echo $d['server']; ?>"
					data-query="<?php echo $d['data']; ?>" data-aksi="<?php echo $d['aksi']; ?>"
					class="sinkron btn <?php echo $btn; ?> btn-block btn-xs"><?php echo $text; ?></a></td>
		</tr>
		<?php } ?>
	</tbody>
</table>
@else
<div class="callout callout-danger lead">
	Anda terhubung ke server direktorat.<br />
	{{ $dapodik->message }} <br>
	Klik <a href="{{route('atur_password_dapodik')}}" class="toggle-modal">disini</a> untuk memperbaharui password dapodik!
</div>
@endif
@else
<div class="callout callout-danger lead">Anda tidak terhubung ke server direktorat.<br />Pastikan PC/Laptop Anda
	terhubung ke internet!</div>
@endif
<div id="modal_content" class="modal fade"></div>
@stop

@section('js')
<script>
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
var BarWidth = setInterval(frame, 1000);
var ProsesSinkronTable;
var Proses_Sinkron = function(m) {
	var url = '{{url('sinkronisasi/proses-artisan/')}}/'+m.server+'/'+m.data+'/'+m.aksi+'/'+m.satuan;
	$.get(url).done(function(response) {
		var data = $.parseJSON(response);
		if(data.status){
			$('.progress-bar').css('width',data.progress+'%');
			StatusText = $('.status').text();
			Proses_Sinkron(data);
			ProsesSinkronTable = data.data;
		} else {
			const wrapper = document.createElement('div');
			wrapper.innerHTML = data.message;
			clearInterval(BarWidth);
			$('.progress-bar').css('width','100%');
			$('.status').text('Proses sinkronisasi data selesai');
			swal({title: 'Selesai', icon: data.icon, content: wrapper, closeOnClickOutside: false}).then((result) => {
				window.location.replace('{{url('sinkronisasi/dapodik/')}}');
			});
		}
	});
}
function Data_Sync(server, data, aksi, satuan){
	this.server = server,
	this.data = data,
	this.aksi = aksi,
	this.satuan = satuan
}
function frame() {
	if(typeof ProsesSinkronTable != 'undefined'){
		$.ajax({
			url: "{{url('sinkronisasi/hitung-data/')}}/"+ProsesSinkronTable,
			success:function(response){
				console.log(response);
				if(response){
					var data = $.parseJSON(response);
					$('.status').text("Memproses sinkronisasi data "+data.table+" ("+data.jumlah+"/"+data.inserted+")");
				}
			}
		});
	}
}
$('#sinkron').click(function(){
	$('#spinner').remove();
	$('.progress').show();
	$('.status').show();
	$('.progress-bar').css('width','10%');
	//var DataSync = new Data_Sync("erapor_server", "jurusan", "jurusan", 0);
	var DataSync = new Data_Sync("erapor_server", "sekolah", "sekolah", 0);
	Proses_Sinkron(DataSync);
});
$('.sinkron').click(function(){
	$('#spinner').remove();
	$('.progress').show();
	$('.status').show();
	var server = $(this).data('server');
	var data = $(this).data('query');
	var aksi = $(this).data('aksi');
	var DataSync = new Data_Sync(server, data, aksi, 1);
	Proses_Sinkron(DataSync);
	ProsesSinkronTable = data;
});
</script>
@stop