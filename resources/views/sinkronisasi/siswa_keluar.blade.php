@extends('adminlte::page')

@section('content_header')
    <h1>Sinkonisasi Dapodik</h1>
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
					<th class="text-center">Nama Siswa</th>
					<th class="text-center">Rombongan Belajar</th>
					<th class="text-center">Status Siswa</th>
					<th class="text-center">Status Anggota Rombel</th>
					<th class="text-center">Status User</th>
	            </tr>
            </thead>
			<tbody>
			<?php
				$no = ($currentPage - 1) * $per_page + 1;
				if($dapodik->total()){
					foreach($dapodik as $data){
						$siswa_id 			= 0;
						$anggota_rombel_id 	= 0;
						$user_id 			= 0;
						$rombongan_belajar_id = 0;
						$result_1 			= 'Abaikan';
						$result_2 			= 'Abaikan';
						$result_3 			= 'Abaikan';
						$class 				= 'table-success"';
						$nama_siswa			= '-';
						$rombongan_belajar = App\Rombongan_belajar::where('rombel_id_dapodik', '=', $data->rombongan_belajar_id)->first();
						$find_siswa = App\Siswa::where('siswa_id_dapodik', '=', $data->peserta_didik_id)->first();
						if($find_siswa){
							$nama_siswa = $find_siswa->nama;
							$class = 'table-danger"';
							$find_anggota_rombel = App\Anggota_rombel::where('siswa_id' , '=', $find_siswa->siswa_id)->first();
							if($find_anggota_rombel){
								if($find_anggota_rombel->delete()){
									$result_2 = 'Terhapus';
								}
							}
							$find_user = App\User::where('siswa_id', '=', $find_data_siswa->siswa_id)->first();
							if($find_user){
								if($find_user->delete()){
									$result_3 = 'Terhapus';
								}
							}
							if($find_siswa->delete()){
								$result_1 = 'Terhapus';
							}
						}
			?>
				<tr class="<?php echo $class; ?>">
					<td class="text-center"><?php echo $no++; ?></td>
					<td><?php echo $nama_siswa; ?></td>
					<td class="text-center"><?php echo ($rombongan_belajar) ? $rombongan_belajar->nama : '-'; ?></td>
					<td class="text-center"><?php echo $result_1; ?></td>
					<td class="text-center"><?php echo $result_2; ?></td>
					<td class="text-center"><?php echo $result_3; ?></td>
				</tr>
			<?php
			}
			//break; 
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
		window.location.replace('<?php echo url('sinkronisasi/ambil-data'); ?>');
	}
})
</script>
@Stop