<?php
if($rombongan_belajar->kunci_nilai){
?>
<script>
$('#simpan').remove();
</script>
<div class="alert alert-danger alert-dismissible">
	<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
	<h4><i class="icon fa fa-info"></i> Informasi!</h4>
	Status penilaian tidak aktif. Untuk menambah atau memperbaharui penilaian remedial, silahkan menghubungi wali kelas.
</div>
<?php
} else {
?>
<div class="row">
	<div class="col-md-6">
		<table class="table table-bordered">
			<tr>
				<td colspan="2" class="text-center"><strong>Keterangan</strong></td>
			</tr>
			<tr>
				<td width="30%">SKM</td>
				<td>{{$kkm}}</td>
			</tr>
			<tr>
				<td><input type="text" class="bg-red form-control input-sm" /></td>
				<td>Tidak tuntas (input aktif)</td>
			</tr>
			<tr>
				<td><input type="text" class="bg-green form-control input-sm" /></td>
				<td>Tuntas (input non aktif)</td>
			</tr>
		</table>
	</div>
</div>
<table class="table table-bordered table-hover" id="remedial">
	<thead>
		<tr>
			<th rowspan="2" style="vertical-align: middle;">Nama Peserta Didik</th>
			<th class="text-center" colspan="{{count($all_kd)}}">Kompetensi Dasar</th>
			<th rowspan="2" style="vertical-align: middle;" class="text-center">Rerata Akhir</th>
			<th rowspan="2" style="vertical-align: middle;" class="text-center">Rerata Remedial</th>
			<th rowspan="2" style="vertical-align: middle;" class="text-center">Hapus</th>
		</tr>
		<tr>
			@foreach($all_kd as $kd)
				<?php
				//dd($kd);
				$kd_id[$kd->kompetensi_dasar_id] = $kd->kompetensi_dasar_id;
				$kompetensi_dasar = ($kd->kompetensi_dasar->kompetensi_dasar_alias) ? $kd->kompetensi_dasar->kompetensi_dasar_alias : $kd->kompetensi_dasar->kompetensi_dasar;
				?>
				<th class="text-center">
					<a href="javacript:void(0)" data-toggle="tooltip" data-placement="left" data-html="true" title="<b>ID {{$kd->kd_id}}</b> &raquo; {{trim(strip_tags($kompetensi_dasar))}}">{{$kd->id_kompetensi}}</a>
				</th>
			@endforeach
			</tr>
		</thead>
		<tbody>
		@foreach($all_siswa as $siswa)
			<tr>
				<td>
					{{strtoupper($siswa->siswa->nama)}}
					<input type="hidden" name="siswa_id[]" value="{{$siswa->anggota_rombel_id}}" />
				</td>
				@foreach($siswa->{$with_1} as $nilai_kd)
					<?php 
					//dd($nilai_kd);
					$kd_nilai[] = $nilai_kd->kompetensi_dasar_id;
					$nilai[$siswa->anggota_rombel_id][$nilai_kd->kompetensi_dasar_id] = $nilai_kd->nilai_kd; 
					?>
				@endforeach
				<?php $set_rerata = 0; ?>
				@if($siswa->nilai_remedial)
					<?php 
					$nilai_remedial = unserialize($siswa->nilai_remedial->nilai); 
					$link_delete = '<a href="'.url('penilaian/delete-remedial/'.$siswa->nilai_remedial->nilai_remedial_id).'" class="confirm_remed btn btn-sm btn-danger"><i class="fa fa-trash"></i></a>';
					?>
					@foreach($nilai_remedial as $kd_id => $nilai_perkd)
					<?php
					$kd_remedial[] = $kd_id;
					if(!$nilai_perkd){
						$nilai_asli_perkd = (isset($nilai[$siswa->anggota_rombel_id][$kd_id])) ? $nilai[$siswa->anggota_rombel_id][$kd_id] : 0;
					}
					$set_rerata += $nilai_perkd;
					if($kkm > number_format($nilai_perkd,0)){
						$aktif = '';
						$bg = 'bg-red';
					} else {
						$aktif = 'readonly';
						$bg = 'bg-green';
					}
					?>
					<td class="text-center"><?php echo ($nilai_perkd) ? '<input type="text" name="nilai_remedial['.$siswa->anggota_rombel_id.']['.$kd_id.']" size="10" class="'.$bg.' form-control input-sm" value="'.number_format($nilai_perkd,0).'" '.$aktif.' />' : '<input type="text" name="nilai_remedial['.$siswa->anggota_rombel_id.']['.$kd_id.']" size="10" class="'.$bg.' form-control input-sm" value="'.number_format($nilai_asli_perkd,0).'" '.$aktif.' />'; ?></td>
					@endforeach
					<?php
					$rerata_akhir = ($siswa->{$with_2}) ? $siswa->{$with_2}->nilai_akhir : 0;//$siswa->nilai_remedial->rerata_akhir;
					$rerata_remedial = $siswa->nilai_remedial->rerata_remedial;
					if($kkm > $rerata_akhir){
						$bg_rerata_akhir = 'text-red';
					} else {
						$bg_rerata_akhir = 'text-green';
					}
					if($kkm > $rerata_remedial){
						$bg_rerata_remedial = 'text-red';
					} else {
						$bg_rerata_remedial = 'text-green';
					}
					?>
				@else
					<?php $link_delete = '-'; ?>
					@foreach($all_kd as $kd)
					<?php
					$kd_id_check[] = $kd->kompetensi_dasar_id;
					$nilai_perkd = (isset($nilai[$siswa->anggota_rombel_id][$kd->kompetensi_dasar_id])) ? $nilai[$siswa->anggota_rombel_id][$kd->kompetensi_dasar_id] : 0;
					$set_rerata += $nilai_perkd;
					if($kkm > number_format($nilai_perkd,0)){
						$aktif = '';
						$bg = 'bg-red';
					} else {
						$aktif = 'readonly';
						$bg = 'bg-green';
					}
					?>
					<td class="text-center"><?php echo ($nilai_perkd) ? '<input type="text" name="nilai_remedial['.$siswa->anggota_rombel_id.']['.$kd->kompetensi_dasar_id.']" size="10" class="'.$bg.' form-control input-sm" value="'.number_format($nilai_perkd,0).'" '.$aktif.' />' : '<input type="hidden" name="nilai_remedial['.$siswa->anggota_rombel_id.']['.$kd->kompetensi_dasar_id.']" value="0" />-'; ?></td>
					@endforeach
					<?php
					$a = ($siswa->{$with_2}) ? $siswa->{$with_2}->where('anggota_rombel_id', $siswa->anggota_rombel_id)->where('pembelajaran_id', $pembelajaran_id)->where('kompetensi_id', $kompetensi_id)->first() : NULL;
					if($set_rerata && $a){
						$rerata_akhir = $a->nilai_akhir;//number_format($set_rerata / $all_kd->count(),0);
						$rerata_remedial = 0;
					} else {
						$rerata_akhir = '';
						$rerata_remedial = '';
					}
					if($kkm > $rerata_akhir){
						$bg_rerata_akhir = 'text-red';
					} else {
						$bg_rerata_akhir = 'text-green';
					}
					if($kkm > $rerata_remedial){
						$bg_rerata_remedial = 'text-red';
					} else {
						$bg_rerata_remedial = 'text-green';
					}
					?>
				@endif
				@if($all_kd->count())
				<input type="hidden" id="rerata_akhir_input" name="rerata_akhir[{{$siswa->anggota_rombel_id}}]" value="<?php echo $rerata_akhir; ?>" />
				<input type="hidden" id="rerata_remedial_input" name="rerata_remedial[{{$siswa->anggota_rombel_id}}]" value="<?php echo $rerata_remedial; ?>" />
				<td id="rerata_akhir" class="text-center <?php echo $bg_rerata_akhir; ?>">
					<strong>
					{{($siswa->{$with_2}) ? $siswa->{$with_2}->nilai_akhir : 0}}
					</strong>
				</td>
				<td id="rerata_remedial" class="text-center <?php echo $bg_rerata_remedial; ?>">
					<strong>
					<?php echo $rerata_remedial; ?>
					</strong>
					</td>
				<td class="text-center"><?php echo $link_delete; ?></td>
				@else
				<td class="text-center">Belum dilakukan penilaian</td>
				<td class="text-center">-</td>
				<td class="text-center">-</td>
				<td class="text-center">-</td>
				@endif
			</tr>
		@endforeach
		</tbody>
</table>
<input type="hidden" id="get_kkm" value="{{$kkm}}" />
<input type="hidden" id="get_all_kd" value="{{$all_kd->count()}}" />
<link  rel="stylesheet" href="{{ asset('vendor/adminlte/plugins/tooltip/tooltip-viewport.css') }}">
<script src="{{ asset('vendor/adminlte/plugins/tooltip/tooltip-viewport.js') }}"></script>
<script>
$('[data-toggle="tooltip"]').tooltip();
var formData = '';
var get_all_kd = $('#get_all_kd').val();
var get_kkm = $('#get_kkm').val();
$("input[type=text]").on("keyup", function() {
	var textVal = 0;
	var $tr = $(this).closest("tr");
	var $input = $tr.find("input:text").each(function() {
		if(parseInt(this.value) >= parseInt(get_kkm)){
			$(this).removeClass('bg-red').addClass('bg-green');
		}
		if(parseInt(this.value) < parseInt(get_kkm)){
			$(this).removeClass('bg-green').addClass('bg-red');
		}
		textVal += parseInt(this.value);
	});
	var $set_rerata_akhir = textVal / parseInt(get_all_kd);
	$tr.find('#rerata_remedial_input').val($set_rerata_akhir.toFixed(0));
	$tr.find('#rerata_remedial').html('<strong>'+$set_rerata_akhir.toFixed(0)+'</strong>');
	if($set_rerata_akhir.toFixed(0) >= parseInt(get_kkm)){
		$tr.find('#rerata_remedial').removeClass('text-red').addClass('text-green');
	}
	if($set_rerata_akhir.toFixed(0) < parseInt(get_kkm)){
		$tr.find('#rerata_remedial').removeClass('text-green').addClass('text-red');
	}
});
$('a.confirm_remed').bind('click',function(e) {
	e.preventDefault();
	var ini = $(this).parents('tr');
	var url = $(this).attr('href');
	swal({
		title: "Anda Yakin?",
		text: "Tindakan ini tidak bisa dikembalikan!",
		icon: "warning",
		buttons: true,
		dangerMode: true,
	}).then((willDelete) => {
		if (willDelete) {
			$.get(url).done(function(response) {
				var data = $.parseJSON(response);
				swal(data.title, {
					icon: data.icon,
				}).then((data) => {
					//console.log(data);
					$.ajax({
						url: '{{url('/ajax/get-remedial')}}',
						type: 'post',
						data: $("form#form").serialize(),
						success: function(response){
							$('#simpan').show();
							$('#result').html(response);
						}
					});
				});
			});
		}
	});
});
@if(!$all_kd->count())
$('#simpan').hide();
@else
$('#simpan').show();
@endif
</script>
<?php } ?>