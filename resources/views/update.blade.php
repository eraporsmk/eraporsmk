@extends('adminlte::page')
@section('title_postfix', 'Cek Pembaharuan | ')
@section('content_header')
    <h1>Cek Pembaharuan</h1>
@stop

@section('content')
	<div id="update_notification" class="callout callout-info">
		<h4>Memerika Pembaharuan</h4>
		<p class="p1">Silahkan tunggu beberapa saat, aplikasi sedang memeriksa pembaharuan di server</p>
		<p class="p2" style="display:none"><a id="check_update" href="javascript:void(0)" class="btn btn-lg btn-warning" style="text-decoration:none;">Proses Pembaharuan</a></p>
	</div>
	<input type="hidden" id="versionAvailable" value="">
	<input type="hidden" id="zipball_url" value="">
	<table class="table table-bordered" id="result" style="display:none;">
		<tr>
			<td width="30%">Mengunduh File Updater</td>
			<td width="70%">
				<div class="progress">
					<div id="download" class="progress-bar progress-bar-warning progress-bar-striped" role="progressbar" style="width: 0%"></div>
				</div>
			</td>
		</tr>
		<tr>
			<td>Mengekstrak File Updater</td>
			<td><span class="extract_to"><p class="text-red"><strong>[MENUNGGU]</strong></p></span></td>
		</tr>
		<tr>
			<td>Memproses Pembaharuan</td>
			<td><span class="update_versi"><p class="text-red"><strong>[MENUNGGU]</strong></p></span></td>
		</tr>
	</table>
	<div id="updater"></div>
	<!--a class="btn btn-success" id="sukses" href="<?php echo url()->current();?>" style="display:none;">Muat Ulang Aplikasi</a-->
@stop


@section('js')
<script type="text/javascript">
$(document).ready(function() {  
	$.ajax({
		type: 'GET',   
		url: '{{route('updater.check')}}',
		async: false,
		success: function(response) {
			if(response.server){
				if(response.new_version){
					$('#versionAvailable').val(response.new_version);
					$('#zipball_url').val(response.zipball_url);
					$('#update_notification h4').html('Pembaharuan Tersedia');
					$('.callout-info').switchClass( "callout-info", "callout-success", 0, "easeInOutQuad" );
					$('#update_notification .p1').html('Gunakan Tombol di bawah ini untuk memperbaharui aplikasi');
					$('#update_notification .p2').show();
				} else {
					$('#update_notification h4').html('Pembaharuan Belum Tersedia');
					$('.callout-info').switchClass( "callout-info", "callout-danger", 0, "easeInOutQuad" );
					$('#update_notification .p1').html('Belum tersedia pembaharuan untuk versi aplikasi Anda');
				}
			} else {
				$('#update_notification h4').html('Memerika Pembaharuan Gagal');
				$('.callout-info').switchClass( "callout-info", "callout-danger", 0, "easeInOutQuad" );
				$('#update_notification .p1').html('Server tidak merespon. Silahkan segarkan kembali halaman ini.');
			}
		}
	});
});
function frame() {
	$.ajax({
		url: "{{route('updater.persentase')}}",
		success:function(response){
			$('#download').addClass('active');
			if(response.percent){
				$('#download.progress-bar').css('width',response.percent+'%');
			}
		}
	});
}
$('#update_notification').find('a').click(function(e){
	e.preventDefault();
	$('#result').show();
	$('#spinner').remove();
	var BarWidth = setInterval(frame, 1000);
	var versionAvailable = $('#versionAvailable').val();
	var zipball_url = $('#zipball_url').val();
	$.get('{{route('updater.download')}}', {versionAvailable: versionAvailable, zipball_url: zipball_url }).done(function( data ) {
		clearInterval(BarWidth);
		$('#download.progress-bar').css('width','100%');
		$('.extract_to').html('<p class="text-yellow"><strong>[PROSES]</strong></p>');
		if(data.next){
			$.get('{{route('updater.extract')}}', {storageFilename: data.storageFilename, storageFolder: data.storageFolder, versionAvailable: data.versionAvailable }).done(function( data_extract ) {
				$('.extract_to').html('<p class="text-green"><strong>[BERHASIL]</strong></p>');
				$('.update_versi').html('<p class="text-yellow"><strong>[PROSES]</strong></p>');
				if(data_extract.next){
					$.get('{{route('updater.proses')}}', {releaseFolder: data_extract.releaseFolder, releaseName: data_extract.releaseName }).done(function( data_proses ) {
						$('.update_versi').html('<p class="text-green"><strong>[BERHASIL]</strong></p>');
						swal({
							title:'Sukses',
							icon:'success',
							content:'Berhasil memperbarui aplikasi',
							button:'Muat Ulang Aplikasi',
							closeOnClickOutside: false,
						}).then((value) => {
							window.location.replace('<?php echo url()->current(); ?>');
						});
					});
				} else {
					swal({
						title:'Gagal',
						icon:'error',
						content:data.status,
						button:'Muat Ulang Aplikasi',
						closeOnClickOutside: false,
					}).then((value) => {
						window.location.replace('<?php echo url()->current(); ?>');
					});
				}
			});
		} else {
			swal({
				title:'Gagal',
				icon:'error',
				content:data.status,
				button:'Muat Ulang Aplikasi',
				closeOnClickOutside: false,
			}).then((value) => {
				window.location.replace('<?php echo url()->current(); ?>');
			});
		}
	});
	/*$.get(url).done(function(response) {
		
	});*/
	return false;
});
/*$('#check_update').click(function(){
	$('#result').show();
	$.ajax({
		url: '<?php echo url('proses-update');?>',
		type: 'get',
		success: function(response){
			var data = $.parseJSON(response);
			$('.download').html(data.text);
			if(data.md5_file_local !== data.md5_file_server){
				swal({
					title:'Gagal',
					icon:'error',
					content:'Gagal mengunduh file updater. Silahkan coba lagi!',
					button:'Muat Ulang Aplikasi',
					closeOnClickOutside: false,
				}).then((value) => {
					window.location.replace('<?php echo url()->current(); ?>');
				});
				return false;
			}
			$.ajax({
				url: '<?php echo url('ekstrak');?>',
				type: 'get',
				success: function(response){
					var data = $.parseJSON(response);
					$('.extract_to').html(data.text);
					if(data.response === 0){
						swal({
							title:'Gagal',
							icon:'error',
							content:'Gagal Mengekstrak File Updater. Silahkan coba lagi!',
							button:'Muat Ulang Aplikasi',
							closeOnClickOutside: false,
						}).then((value) => {
							window.location.replace('<?php echo url()->current(); ?>');
						});
						return false;
					}
					$.ajax({
						url: '<?php echo url('update-versi');?>',
						type: 'get',
						success: function(response){
							console.log(response);
							var data = $.parseJSON(response);
							$('.update_versi').html(data.text);
							if(data.response === 0){
								swal({
									title:'Gagal',
									icon:'error',
									content:'Gagal Memproses Pembaharuan. Silahkan coba lagi!',
									button:'Muat Ulang Aplikasi',
									closeOnClickOutside: false,
								}).then((value) => {
									window.location.replace('<?php echo url()->current(); ?>');
								});
								return false;
							}
							window.setTimeout(function() {
								swal({
									title:'Sukses',
									icon:'success',
									content:'Berhasil memperbarui aplikasi',
									button:'Muat Ulang Aplikasi',
									closeOnClickOutside: false,
								}).then((value) => {
									window.location.replace('<?php echo url()->current(); ?>');
								});
							}, 1000);
						}
					});
				}
			});
		}
	});
})*/
</script>
@stop