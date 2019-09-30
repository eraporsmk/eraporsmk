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
					<th class="text-center">Nama Ekskul</th>
					<th class="text-center">Nama Pembina</th>
					<th class="text-center">Prasarana</th>
					<th class="text-center">status</th>
	            </tr>
            </thead>
			<tbody>
			<?php
				$no = ($currentPage - 1) * $per_page + 1;
				foreach($dapodik as $data){
					$find_rombel_erapor = App\Rombongan_belajar::where('rombel_id_dapodik', $data->rombongan_belajar_id)->first();
					$get_wali = App\Guru::where('guru_id_dapodik', $data->ptk_id)->first();
					$find_kelas_ekskul = App\Ekstrakurikuler::where('semester_id', '=', $semester->semester_id)->where('id_kelas_ekskul', '=', $data->ID_kelas_ekskul)->first();
					$insert_rombel = array(
						'semester_id' 			=> $semester->semester_id,
						'sekolah_id' 			=> $sekolah->sekolah_id,
						'kurikulum_id' 			=> $data->kurikulum_id,
						'nama' 					=> $data->nama,
						'guru_id' 				=> $get_wali->guru_id,
						'tingkat' 				=> $data->tingkat_pendidikan_id,
						'guru_id_dapodik' 		=> $data->ptk_id,
						'rombel_id_dapodik'		=> $data->rombongan_belajar_id,
						'jenis_rombel'			=> $data->jenis_rombel,
						'last_sync'				=> date('Y-m-d H:i:s'),
					);
					if($find_rombel_erapor){
						$rombongan_belajar_id = $find_rombel_erapor->rombongan_belajar_id;
						App\Rombongan_belajar::find($find_rombel_erapor->rombongan_belajar_id)->update($insert_rombel);
						$result = 'update';
					} else {
						$rombongan_belajar = App\Rombongan_belajar::create($insert_rombel);
						$rombongan_belajar_id = $rombongan_belajar->rombongan_belajar_id;
						$result = 'insert';
					}
					$data_ekskul = array(
						'sekolah_id'	=> $sekolah->sekolah_id,
						'semester_id' => $semester->semester_id,
						'guru_id' => $get_wali->guru_id,
						'nama_ekskul' => $data->nm_ekskul,
						'is_dapodik' => 1,
						'rombongan_belajar_id'	=> $rombongan_belajar_id,
						'id_kelas_ekskul' => $data->ID_kelas_ekskul, 
						'alamat_ekskul' => $data->nama_prasarana, 
						'last_sync'	=> date('Y-m-d H:i:s'),
					);
					if($find_kelas_ekskul){
						$result = 'update';
						$class = 'table-danger"';
						App\Ekstrakurikuler::find($find_kelas_ekskul->ekstrakurikuler_id)->update($data_ekskul);
					} else {
						$result = 'insert';
						$class = 'table-success"';
						App\Ekstrakurikuler::create($data_ekskul);
					}
			?>
				<tr class="<?php echo $class; ?>">
					<td class="text-center"><?php echo $no++; ?></td>
					<td><?php echo $data->nm_ekskul; ?></td>
					<td class="text-center"><?php echo $data->nama_pembina; ?></td>
					<td class="text-center"><?php echo $data->nama_prasarana; ?></td>
					<td class="text-center"><?php echo $result; ?></td>
				</tr>
			<?php
			//break; 
			} ?>
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