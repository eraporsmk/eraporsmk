@extends('adminlte::page')

@section('content_header')
    <h1>Sinkonisasi Anggota Ekskul</h1>
@stop

@section('content')
	@role('superadmin')
		<p>This is visible to users with the admin role. Gets translated to
		\Laratrust::hasRole('superadmin')</p>
	@endrole
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
		<table class="table table-bordered table-striped table-hover">
            <thead>
				<tr>
					<th class="text-center">No</th>
					<th class="text-center">Nama</th>
					<th class="text-center">Tanggal Lahir</th>
					<th class="text-center">NISN</th>
					<th class="text-center">Nama Ekskul</th>
					<th class="text-center">status</th>
	            </tr>
            </thead>
			<tbody>
			<?php
				$no = ($currentPage - 1) * $per_page + 1;
				if($dapodik->total()){
					foreach($dapodik as $data){
						$find_rombel = App\Rombongan_belajar::where('rombel_id_dapodik', '=', $data->rombongan_belajar_id)->first();
						if($find_rombel){
							$rombongan_belajar_id = $find_rombel->rombongan_belajar_id;
						} else {
							$get_wali = App\Guru::where('guru_id_dapodik', '=', $data->ptk_id)->first();
							$insert_rombel = array(
								'semester_id' 			=> $semester->semester_id,
								'sekolah_id' 			=> $sekolah->sekolah_id,
								'kurikulum_id' 			=> $data->kurikulum_id,
								'nama' 					=> $data->nama_rombel,
								'guru_id' 				=> $get_wali->guru_id,
								'tingkat' 				=> $data->tingkat_pendidikan_id,
								'guru_id_dapodik' 		=> $data->ptk_id,
								'rombel_id_dapodik'		=> $data->rombongan_belajar_id,
								'jenis_rombel'			=> $data->jenis_rombel,
								'last_sync'				=> date('Y-m-d H:i:s'),
							);
							$create_rombel = App\Rombongan_belajar::create($insert_rombel);
							$rombongan_belajar_id = $create_rombel->rombongan_belajar_id;
						}
						$find_siswa = App\Siswa::where('peserta_didik_id_dapodik', '=', $data->peserta_didik_id)->first();
						if($find_siswa){
							$find_anggota_rombel = App\Anggota_rombel::where('anggota_rombel_id_dapodik', $data->anggota_rombel_id)->first();
							$attributes_update_anggota_rombel = array(
								'sekolah_id'	=> $sekolah->sekolah_id,
								'semester_id' 				=> $semester->semester_id, 
								'rombongan_belajar_id' 		=> $rombongan_belajar_id, 
								'peserta_didik_id' 			=> $find_siswa->peserta_didik_id,
								'anggota_rombel_id_dapodik'	=> $data->anggota_rombel_id,
								'last_sync'			=> date('Y-m-d H:i:s'),
							);
							if($find_anggota_rombel){
								App\Anggota_rombel::find($find_anggota_rombel->anggota_rombel_id)->update($attributes_update_anggota_rombel);
								$result = 'update';
								$class = 'table-danger"';
							} else {
								App\Anggota_rombel::create($attributes_update_anggota_rombel);
								$result = 'insert';
								$class = 'table-success"';
							}
						} else {
							$result = 'ignore';
							$class = 'table-warning"';
						}
			?>
				<tr class="<?php echo $class; ?>">
					<td class="text-center"><?php echo $no++; ?></td>
					<td><?php echo $data->nama_siswa; ?></td>
					<td class="text-center"><?php echo date('d-m-Y',strtotime($data->tanggal_lahir)); ?></td>
					<td class="text-center"><?php echo $data->nisn; ?></td>
					<td class="text-center"><?php echo $data->nama_rombel; ?></td>
					<td class="text-center"><?php echo $result; ?></td>
				</tr>
			<?php
				}
			} else { ?>
				<tr>
					<td class="text-center" colspan="6">Tidak ada data untuk ditampilkan</td>
				</tr>
			<?php } ?>
			</tbody>
		</table>
		<div class="text-center">{{ $dapodik->links() }}</div>
@Stop
@section('js')
<script>
$(document).ready(function(){
	$('body').mouseover(function(){
		$(this).css({cursor: 'progress'});
	});
	var cari = $('body').find('a[rel=next]');
	if(cari.length>0){
		var url = $(cari).attr('href');
		console.log(url);
		window.location.replace(url);
	} else {
		window.location.replace('<?php echo url('referensi/ekskul'); ?>');
	}
})
</script>
@Stop