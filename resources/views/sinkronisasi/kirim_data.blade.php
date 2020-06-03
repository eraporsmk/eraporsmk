@extends('adminlte::page')

@section('content_header')
    <h1>Sinkronisasi Data eRapor</h1>
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
	{{-- config('site.last_sync') --}}
	<style>
    #progress {
      width: 500px;
      border: 1px solid #aaa;
      height: 20px;
    }
    #progress .bar {
      background-color: #ccc;
      height: 20px;
    }
  </style>
<div class="row">
	<div class="col-md-12">
		<div class="box box-primary">
			<div class="box-header">
				<i class="fa fa-bank"></i>
				<h3 class="box-title">Identitas Sekolah</h3>
			</div>
			<div class="box-body">
				<table class="table">
					<tr>
						<th width="40%">NPSN Sekolah</th>
						<th width="1%">:</th>
						<th width="55%"><?php echo ($sekolah) ? $sekolah->npsn : '-'; ?></th>
					</tr>
					<tr>
						<th width="40%">Nama Sekolah</th>
						<th width="1%">:</th>
						<th width="55%"><?php echo ($sekolah) ? $sekolah->nama : '-'; ?></th>
					</tr>
					<tr>
						<th width="40%">Alamat Sekolah</th>
						<th width="1%">:</th>
						<th width="55%"><?php echo ($sekolah) ? $sekolah->alamat : '-'; ?></th>
					</tr>
					<tr>
						<th width="40%">Kepala Sekolah</th>
						<th width="1%">:</th>
						<th width="55%"><?php echo ($sekolah) ? CustomHelper::nama_guru($sekolah->guru->gelar_depan, $sekolah->guru->nama, $sekolah->guru->gelar_belakang) : '-'; ?></th>
					</tr>
				</table>
			</div><!-- /.box-body -->
		</div><!-- /.box -->
	</div>
</div>
<?php
$max_input_vars = ini_get('max_input_vars');
$last_sync = config('global.last_sync');
$status = checkdnsrr('php.net');
$connect = ($status) ? 'bg-green' : 'bg-red';
$text = ($status) ? 'TERHUBUNG' : 'TIDAK TERHUBUNG';
$tombol = ($status) ? 'ajax' : 'disabled';
if($status){
	if(!$status_sync->server){
		$status = FALSE;
		$connect = ($status) ? 'bg-green' : 'bg-red';
		$text = ($status) ? 'TERHUBUNG' : 'Pengiriman data ditutup sementara';
		$tombol = ($status) ? 'ajax' : 'disabled';
	}
}
//$table_sync = CustomHelper::table_sync();
$table_sync = config('erapor.table_sync');
?>
<div class="row">
	<div class="col-md-12">
		<div class="box box-warning">
			<div class="box-header">
				<i class="fa fa-signal"></i>
				<h3 class="box-title"> STATUS KONEKSI : <span class="label <?php echo $connect; ?>"><?php echo $text; ?></span></h3>
			</div>
			<div class="bg-black-active color-palette status_sync" style="padding:5px; font-size:120%; text-align:center; display:none;"><span class="status_text">Mempersiapkan pengiriman data</span></div>
			<div class="box-body text-center">
				<p>Pengiriman data dilakukan terakhir <strong>{{CustomHelper::TanggalIndo($last_sync)}}</strong></p>
				<p><button type="button" id="kirim_nilai" class="btn btn-success btn-lg <?php echo $tombol; ?>" title="KIRIM DATA" data-loading-text="<i class='fa fa-spinner fa-spin '></i> &nbsp; SEDANG PROSES SINKRONISASI"><i class="fa fa-refresh"></i>&nbsp; SINKRONISASI</button></p>
				<div id="kirim_data" class="progress active" style="display:none;">
					<div class="progress-bar progress-bar-success progress-bar-striped" role="progressbar" aria-valuenow="0"><span class="justify-content-center d-flex position-absolute w-100"></span></div>
				</div>
				<div id="ambil_data" class="progress active" style="display:none;">
					<div class="progress-bar progress-bar-warning progress-bar-striped" role="progressbar" aria-valuenow="0"><span class="justify-content-center d-flex position-absolute w-100"></span></div>
				</div>
				<span class="response_text"></span>
				<span class="response_table"></span>
			</div><!-- /.box-body -->
		</div>
	</div>
</div>
<div class="row">
	<div class="col-md-12">
		<div class="box box-primary">
			<div class="box-header">
				<i class="fa fa-reorder"></i>
				<h3 class="box-title">DATA YANG MENGALAMI PERUBAHAN</h3>
			</div>
			<div class="box-body">
				<?php
				$last_sync_date = date('Y-m-d', strtotime($last_sync));
				$last_sync_time = date('H:i:s', strtotime($last_sync));
				?>
				<table class="table table-bordered">
					<tr>
						<th style="width: 10px">No.</th>
						<th>Table</th>
						<th style="width: 100px" class="text-center">Jumlah Data</th>
					</tr>
					<?php
					$i=1;
					$total = 0;
					$result = 0;
					$no_semester = ['sekolah', 'jurusan_sp', 'guru', 'peserta_didik', 'dudi', 'mou', 'absensi', 'catatan_ppk', 'catatan_wali', 'deskripsi_mata_pelajaran', 'deskripsi_sikap', 'kd_nilai', 'nilai', 'nilai_akhir', 'nilai_ekstrakurikuler', 'nilai_sikap', 'nilai_ukk', 'prakerin', 'prestasi', 'nilai_remedial', 'rencana_penilaian', 'teknik_penilaian', 'bobot_keterampilan', 'nilai_rapor', 'kenaikan_kelas', 'ref.kompetensi_dasar', 'users', 'role_user', 'kewirausahaan', 'anggota_kewirausahaan', 'nilai_un', 'nilai_us'];
					$no_sekolah = ['users', 'ref.kompetensi_dasar', 'role_user', 'anggota_kewirausahaan'];
					foreach($table_sync as $sync){
							$query = DB::table($sync);
							if($sync == 'ref.kompetensi_dasar'){
								$query->whereIn('user_id', function($q){
									$q->select('user_id')->from('users');
								});
							}
							if (!in_array($sync, $no_semester)) {
							//if($sync != 'sekolah'){
								$query->where('semester_id', '=', $semester->semester_id);
							}
							if (Schema::hasColumn($sync, 'last_sync')) {
								$query->where('last_sync', '>=', $last_sync);	
							}
							//if (Schema::hasColumn($sync, 'semester_id')){
								
							//}
							//if (Schema::hasColumn($sync, 'sekolah_id')){
							if (!in_array($sync, $no_sekolah)) {
								$query->where('sekolah_id', '=', $user->sekolah_id);
							}
							$result = $query->count();
							if($result){
								$total += $result;
					?>
					<tr>
						<td class="text-center"><?php echo $i; ?></td>
						<td><?php echo $sync; ?></td>
						<td class="text-right"><?php echo number_format($result,0, '', '.'); ?></td>
					</tr>
					<?php 
								$i++;
							}
						}
					if($total){?>
					<tr>
						<td colspan="2" class="text-right"><strong>T O T A L</strong></td>
						<td class="text-right"><strong><?php echo number_format($total,0, '', '.'); ?></strong></td>
					</tr>
					<?php } else { ?>
					<tr>
						<td class="text-center" colspan="3">Tidak ada data yang mengalami perubahan</td>
					</tr>
					<?php } ?>
				</table>    
			</div><!-- /.box-body -->
		</div><!-- /.box -->
	</div>
</div>
@Stop
@section('js')
<script>
var timer;
function refreshProgress() {
	$.ajax({
		//url: "<?php echo url('checker.php?file='.session()->getId()); ?>",
		//url: "<?php echo url('checker.php?file=sinkronisasi'); ?>",
		url: "<?php echo route('checker', ['file' => 'sinkronisasi']); ?>",
		success:function(data){
			console.log(data);
			if(data.query == 'kirim'){
				$('#kirim_data .progress-bar').attr('aria-valuenow', data.percent).css('width',data.percent+'%');
			} else {
				$('#ambil_data').show();
				$('#ambil_data .progress-bar').attr('aria-valuenow', data.percent).css('width',data.percent+'%');
			}
			$(".status_text").html(data.message);
			/*if (data.percent == 100) {
				window.clearInterval(timer);
				timer = window.setInterval(completed, 1000);
			}*/
		}
	});
}
function completed() {
	$("#message").html("Completed");
	$("#content").html('');
	window.clearInterval(timer);
}
$('.ajax').click(function(){
	$('body').mouseover(function(){
		$(this).css({cursor: 'wait'});
	});
	$("a").each(function() {
    	$(this).data("href", $(this).attr("href"))
        	.attr("href", "javascript:void(0)")
        	.attr("disabled", "disabled");
	});
	$('#status').show();
	$('#spinner').remove();
	$('#kirim_data').show();
	$('.status_sync').show();
	var btn = $(this);
	btn.button('loading');
	$.ajax({
		url: '<?php echo url('sinkronisasi/proses-sync'); ?>',
		success: function(response){
			var result = $.parseJSON(response);
			//$('body').mouseover(function(){
			//	$(this).css({cursor: 'default'});
			//});
			//btn.button('reset');
			//window.clearInterval(timer);
			//timer = window.setInterval(completed, 1000);
			$('#kirim_data .progress-bar').attr('aria-valuenow', '100').css('width','100%');
			/*swal({title:result.title, content:result.text, icon:result.type, closeOnClickOutside: false}).then((value) => {
				window.location.replace('<?php echo url('sinkronisasi/erapor'); ?>');
			});*/
			$(".status_text").html('Mempersiapkan pengambilan data');
			$.ajax({
				url: '<?php echo url('sinkronisasi/proses-ambil-data'); ?>',
				success: function(response){
					//$('.response_text').html(response);
					btn.button('reset');
					window.clearInterval(timer);
					timer = window.setInterval(completed, 1000);
					$('#ambil_data .progress-bar').attr('aria-valuenow', '100').css('width','100%');
					var result = $.parseJSON(response);
					$('body').mouseover(function(){
						$(this).css({cursor: 'default'});
					});
					swal({title:result.title, content:result.text, icon:result.type, closeOnClickOutside: false}).then((value) => {
						window.location.replace('<?php echo url('sinkronisasi/erapor'); ?>');
					});
				}
			});
		}
	});
	timer = window.setInterval(refreshProgress, 1000);
});
<?php if(!$total){?>
$('#kirim_nilai').attr("disabled", true);
<?php } ?>
</script>
@Stop