@extends('adminlte::page')
@section('title_postfix', 'Cek Pembaharuan | ')
@section('content_header')
    <h1>Cek Pembaharuan</h1>
@stop

@section('content')
	<div id="update_notification" class="callout callout-danger">
		<h4>Status Pembaharuan Aplikasi</h4>
		<p class="p1">Belum tersedia pembaharuan untuk versi aplikasi Anda</p>
		<p class="p2" style="display:none"><a id="check_update" href="updater.update" class="btn btn-lg btn-warning" style="text-decoration:none;">Proses Pembaharuan</a></p>
	</div>
	<div id="result"></div>
	<table class="table table-bordered" id="result1" style="display:none;">
		<tr>
			<td>Mengunduh File Updater</td>
			<td><span class="download"><p class="text-yellow"><strong>[PROSES]</strong></p></span></td>
		</tr>
		<tr>
			<td>Mengekstrak File Updater</td>
			<td><span class="extract_to"><p class="text-yellow"><strong>[PROSES]</strong></p></span></td>
		</tr>
		<tr>
			<td>Memproses Pembaharuan</td>
			<td><span class="update_versi"><p class="text-yellow"><strong>[PROSES]</strong></p></span></td>
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
		url: 'updater.check',
		async: false,
		success: function(response) {
			console.log(response);
			if(response != ''){
				$('#update_notification h4').html('Pembaharuan Tersedia');
				$('.callout-danger').switchClass( "callout-danger", "callout-success", 0, "easeInOutQuad" );
				$('#update_notification .p1').html('Gunakan Tombol di bawah ini untuk memperbaharui aplikasi');
				$('#update_notification .p2').show();
				//$('#update_notification').append('<strong>Update Available <span class="badge badge-pill badge-info">v. '+response+'</span></strong><a role="button" href="updater.update" class="btn btn-sm btn-info pull-right">Update Now</a>');
				//$('#update_notification').show();
			}
		}
	});
});
$('#update_notification').find('a').click(function(e){
	e.preventDefault();
	var url = $(this).attr('href');
	$.get(url).done(function(response) {
		$('#result').html(response);
		$('#sukses').show();
		$.get('update-versi').done(function(response) {
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
	});
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