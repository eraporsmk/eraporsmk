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
					<th class="text-center">jenis_kelamin</th>
					<th class="text-center">tempat_lahir</th>
					<th class="text-center">tanggal_lahir</th>
					<th class="text-center">nik</th>
					<th class="text-center">nuptk</th>
					<th class="text-center">email</th>
					<th class="text-center">status</th>
	            </tr>
            </thead>
			<tbody>
			<?php
				$no = ($currentPage - 1) * $per_page + 1;
				foreach($dapodik as $data){				
					$data->nuptk = str_replace(' ','',$data->nuptk);
					$data_sync = array(
						'ptk_id'	=> $data->ptk_id,
					);
					$host_server = 'http://103.40.55.242/erapor_server/sync/rwy_pend_formal';
					$response = Curl::to($host_server)
					->withData($data_sync)
					->post();
					$response = json_decode($response);
					$find_gelar = ($response) ? $response->rwy_pend_formal : '';
					$data_guru = App\Guru::where('guru_id_dapodik', strtolower($data->ptk_id))->first();
					$data->nuptk = str_replace('-','',$data->nuptk);
					$data->nuptk = str_replace(' ','',$data->nuptk);
					if($data_guru){
						$data->nuptk = ($data_guru->nuptk) ? $data_guru->nuptk : mt_rand();
					} else {
						$data->nuptk = ($data->nuptk) ? $data->nuptk : mt_rand();
					}
					$random = Str::random(6);
					$data->email = ($data->email) ? $data->email : strtolower($random).'@erapor-smk.net';
					$data->email = ($data->email != $user->email) ? $data->email : strtolower($random).'@erapor-smk.net';
					$data->email = ($data->email != $sekolah->email) ? $data->email : strtolower($random).'@erapor-smk.net';
					$data->email = strtolower($data->email);
					$kecamatan = App\Mst_wilayah::where('kode_wilayah', $data->kode_wilayah)->first();
					$password = 12345678;
					if($data_guru){
						$result = 'update';
						$class = 'table-danger"';
						$update_guru = array(
							'sekolah_id'			=> $sekolah->sekolah_id,
							'nama' 					=> $data->nama,
							'nuptk' 				=> $data->nuptk,
							'nip' 					=> $data->nip,
							'nik' 					=> $data->nik,
							'jenis_kelamin' 		=> $data->jenis_kelamin,
							'tempat_lahir' 			=> $data->tempat_lahir,
							'tanggal_lahir' 		=> $data->tanggal_lahir,
							'status_kepegawaian_id'	=> $data->status_kepegawaian_id,
							'jenis_ptk_id' 			=> $data->jenis_ptk_id,
							'agama_id' 				=> $data->agama_id,
							'alamat' 				=> $data->alamat_jalan,
							'rt' 					=> ($data->rt) ? $data->rt : 0,
							'rw' 					=> ($data->rw) ? $data->rw : 0,
							'desa_kelurahan' 		=> $data->desa_kelurahan,
							'kecamatan' 			=> $kecamatan->nama,
							'kode_pos'				=> ($data->kode_pos) ? $data->kode_pos : 0,
							'no_hp'					=> ($data->no_hp) ? $data->no_hp : 0,
							'email' 				=> $data->email,
							'photo' 				=> '',
							'guru_id_dapodik' 		=> strtolower($data->ptk_id),
							'last_sync'				=> date('Y-m-d H:i:s'),
						);
						$find_user = App\User::where('guru_id', $data_guru->guru_id)->first();
						if(!$find_user){
							$create_user = App\User::create([
								'name' => $data->nama,
								'email' => $data->email,
								'nuptk'	=> $data->nuptk,
								'password' => Hash::make($password),
								'last_sync'	=> date('Y-m-d H:i:s'),
								'sekolah_id'	=> $sekolah->sekolah_id,
								'password_dapo'	=> md5($password),
								'guru_id'	=> $data_guru->guru_id,
							]);
							if($create_user){
								$adminRole = App\Role::where('name', 'guru')->first();
								$CheckadminRole = DB::table('role_user')->where('user_id', $create_user->user_id)->first();
								if(!$CheckadminRole){
									$create_user->attachRole($adminRole);
								}
							}
						}
						App\Guru::find($data_guru->guru_id)->update($update_guru);
						if($find_gelar){
							$find_gelar = array_unique($find_gelar, SORT_REGULAR);
							foreach($find_gelar as $gelar){
								if($gelar->gelar_akademik_id){
									$find_gelar_ptk = App\Gelar_ptk::where([['ptk_id', '=', $data->ptk_id], ['gelar_akademik_id', '=', $gelar->gelar_akademik_id]])->first();
									if($find_gelar_ptk){
										$find_gelar_ptk->delete();
									}
									App\Gelar_ptk::create(array('gelar_akademik_id' => $gelar->gelar_akademik_id, 'sekolah_id' => $sekolah->sekolah_id, 'ptk_id' => $data->ptk_id, 'guru_id' => $data_guru->guru_id, 'last_sync' => date('Y-m-d H:i:s')));
								}
							}
						}
						$result = 'update';
						$class = 'table-danger"';
					} else {
						$find_user = App\User::where('name', $data->nama)->first();
						if(!$find_user){
							$insert_guru = array(
								'sekolah_id'			=> $sekolah->sekolah_id,
								'nama' 					=> $data->nama,
								'nuptk' 				=> $data->nuptk,
								'nip' 					=> $data->nip,
								'nik' 					=> $data->nik,
								'jenis_kelamin' 		=> $data->jenis_kelamin,
								'tempat_lahir' 			=> $data->tempat_lahir,
								'tanggal_lahir' 		=> $data->tanggal_lahir,
								'status_kepegawaian_id'	=> $data->status_kepegawaian_id,
								'jenis_ptk_id' 			=> $data->jenis_ptk_id,
								'agama_id' 				=> $data->agama_id,
								'alamat' 				=> $data->alamat_jalan,
								'rt' 					=> ($data->rt) ? $data->rt : 0,
								'rw' 					=> ($data->rw) ? $data->rw : 0,
								'desa_kelurahan' 		=> $data->desa_kelurahan,
								'kecamatan' 			=> $kecamatan->nama,
								'kode_pos'				=> ($data->kode_pos) ? $data->kode_pos : 0,
								'no_hp'					=> ($data->no_hp) ? $data->no_hp : 0,
								'email' 				=> $data->email,
								'photo' 				=> '',
								'guru_id_dapodik' 		=> strtolower($data->ptk_id),
								'last_sync'				=> date('Y-m-d H:i:s'),
							);
							$dataguru = App\Guru::create($insert_guru);
							if($dataguru){
								$create_user = App\User::create([
									'name' => $data->nama,
									'email' => $data->email,
									'nuptk'	=> $data->nuptk,
									'password' => Hash::make($password),
									'last_sync'	=> date('Y-m-d H:i:s'),
									'sekolah_id'	=> $sekolah->sekolah_id,
									'password_dapo'	=> md5($password),
									'guru_id'	=> $dataguru->guru_id,
								]);
								if($create_user){
									$adminRole = App\Role::where('name', 'guru')->first();
									$CheckadminRole = DB::table('role_user')->where('user_id', $create_user->user_id)->first();
									if(!$CheckadminRole){
										$create_user->attachRole($adminRole);
									}
								}
							}
							if($find_gelar){
								$find_gelar = array_unique($find_gelar, SORT_REGULAR);
								foreach($find_gelar as $gelar){
									if($gelar->gelar_akademik_id){
										$find_gelar_ptk = App\Gelar_ptk::where([['ptk_id', '=', $data->ptk_id], ['gelar_akademik_id', '=', $gelar->gelar_akademik_id]])->first();
										if($find_gelar_ptk){
											$find_gelar_ptk->delete();
										}
										App\Gelar_ptk::create(array('gelar_akademik_id' => $gelar->gelar_akademik_id, 'sekolah_id' => $sekolah->sekolah_id, 'ptk_id' => $data->ptk_id, 'guru_id' => $dataguru->guru_id, 'last_sync' => date('Y-m-d H:i:s')));
									}
								}
							}
						}
						$result = 'insert';
						$class = 'table-success"';
					}
			?>
				<tr class="<?php echo $class; ?>">
					<td class="text-center"><?php echo $no++; ?></td>
					<td><?php echo $data->nama; ?></td>
					<td><?php echo $data->jenis_kelamin; ?></td>
					<td><?php echo $data->tempat_lahir; ?></td>
					<td><?php echo $data->tanggal_lahir; ?></td>
					<td><?php echo $data->nik; ?></td>
					<td><?php echo $data->nuptk; ?></td>
					<td><?php echo $data->email; ?></td>
					<td><?php echo $result; ?></td>
				</tr>
			<?php
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