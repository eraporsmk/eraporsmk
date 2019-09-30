@extends('adminlte::page')

@section('content_header')
    <h1>Sinkonisasi Dapodik</h1>
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
		<table class="table table-bordered table-striped table-hover">
            <thead>
				<tr>
					<th class="text-center" style="vertical-align: middle;">No</th>
					<th class="text-center">nama</th>
					<th class="text-center">jurusan_sp_id</th>
					<th class="text-center">kurikulum_id</th>
					<th class="text-center">guru_id</th>
					<th class="text-center">tingkat</th>
					<th class="text-center">status</th>
	            </tr>
            </thead>
			<tbody>
			<?php
				$no = ($currentPage - 1) * $per_page + 1;
				foreach($dapodik as $data){
					$find_rombel_erapor = App\Rombongan_belajar::where('rombel_id_dapodik', '=', $data->rombongan_belajar_id)->first();//->where('semester_id', '=', $semester->semester_id)
					$get_jurusan_id = App\Jurusan_sp::where('jurusan_sp_id_dapodik', '=', $data->jurusan_sp_id)->first();
					$get_wali = App\Guru::where('guru_id_dapodik', '=', $data->ptk_id)->first();
					$get_user = App\User::where('guru_id', '=', $get_wali->guru_id)->first();
					$insert_rombel = array(
						'semester_id' 			=> $semester->semester_id,
						'sekolah_id' 			=> $sekolah->sekolah_id,
						'jurusan_id' 			=> $get_jurusan_id->jurusan_id,
						'jurusan_sp_id' 		=> $get_jurusan_id->jurusan_sp_id,
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
						//$this->rombongan_belajar->update($find_rombel_erapor->rombongan_belajar_id, $insert_rombel);
						App\Rombongan_belajar::find($find_rombel_erapor->rombongan_belajar_id)->update($insert_rombel);
						$result = 'update';
					} else {
						App\Rombongan_belajar::create($insert_rombel);
						$result = 'insert';
					}
					$adminRole = App\Role::where('name', 'wali')->first();
					$CheckadminRole = DB::table('role_user')->where('user_id', $get_user->user_id)->where('role_id', $adminRole->id)->first();
					if(!$CheckadminRole){
						$get_user->attachRole($adminRole);
					}
					$get_jurusan = App\Jurusan::where('jurusan_id', $get_jurusan_id->jurusan_id)->first();
					$get_kurikulum = App\Kurikulum::where('kurikulum_id', $data->kurikulum_id)->first();
			?>
				<tr>
					<td class="text-center"><?php echo $no++; ?></td>
					<td><?php echo $data->nama; ?></td>
					<td><?php echo $get_jurusan->nama_jurusan; ?></td>
					<td><?php echo $get_kurikulum->nama_kurikulum; ?></td>
					<td><?php echo $get_wali->nama; ?></td>
					<td><?php echo $insert_rombel['tingkat']; ?></td>
					<td><?php echo $result; ?></td>
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