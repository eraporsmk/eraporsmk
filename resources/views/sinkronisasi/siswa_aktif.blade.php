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
					<th class="text-center">No</th>
					<th class="text-center">Nama</th>
					<th class="text-center">Tanggal Lahir</th>
					<th class="text-center">No Induk</th>
					<th class="text-center">NISN</th>
					<th class="text-center">status</th>
	            </tr>
            </thead>
			<tbody>
			<?php
				$no = ($currentPage - 1) * $per_page + 1;
				if($dapodik){
					foreach($dapodik as $data){
						$data_sync = array(
							'peserta_didik_id'	=> $data->peserta_didik_id,
							'sekolah_id'		=> $sekolah->sekolah_id,
						);
						$host_server = 'http://103.40.55.242/erapor_server/sync/diterima_kelas';
						$response = Curl::to($host_server)
						->withData($data_sync)
						->post();
						$response = json_decode($response);
						$find_diterima_kelas = isset($response->data->nama) ? $response->data->nama : '-';
						$random = Str::random(6);
						$data->nisn = ($data->nisn) ? $data->nisn : mt_rand();
						$data->email = ($data->email) ? $data->email : strtolower($random).'@erapor-smk.net';
						$data->email = ($data->email != $sekolah->email) ? $data->email : strtolower($random).'@erapor-smk.net';
						$data->email = strtolower($data->email);
						$insert_siswa = array(
							'sekolah_id'		=> $sekolah->sekolah_id,
							'nama' 				=> $data->nama_siswa,
							'no_induk' 			=> ($data->nipd) ? $data->nipd : 0,
							'nisn' 				=> $data->nisn,
							'jenis_kelamin' 	=> ($data->jenis_kelamin) ? $data->jenis_kelamin : 0,
							'tempat_lahir' 		=> ($data->tempat_lahir) ? $data->tempat_lahir : 0,
							'tanggal_lahir' 	=> $data->tanggal_lahir,
							'agama_id' 			=> ($data->agama_id) ? $data->agama_id : 0,
							'status' 			=> 'Anak Kandung',
							'anak_ke' 			=> ($data->anak_keberapa) ? $data->anak_keberapa : 0,
							'alamat' 			=> ($data->alamat_jalan) ? $data->alamat_jalan : 0,
							'rt' 				=> ($data->rt) ? $data->rt : 0,
							'rw' 				=> ($data->rw) ? $data->rw : 0,
							'desa_kelurahan' 	=> ($data->desa_kelurahan) ? $data->desa_kelurahan : 0,
							'kecamatan' 		=> ($data->kecamatan) ? $data->kecamatan : 0,
							'kode_pos' 			=> ($data->kode_pos) ? $data->kode_pos : 0,
							'no_telp' 			=> ($data->nomor_telepon_seluler) ? $data->nomor_telepon_seluler : 0,
							'sekolah_asal' 		=> ($data->sekolah_asal) ? $data->sekolah_asal : 0,
							'diterima_kelas' 	=> ($find_diterima_kelas) ? $find_diterima_kelas : 0,
							'diterima' 			=> ($data->tanggal_masuk_sekolah) ? $data->tanggal_masuk_sekolah : 0,
							'kode_wilayah' 		=> $data->kode_wilayah,
							'email' 			=> ($data->email) ? $data->email : GenerateEmail().'@erapor-smk.net',
							'nama_ayah' 		=> ($data->nama_ayah) ? $data->nama_ayah : 0,
							'nama_ibu' 			=> ($data->nama_ibu_kandung) ? $data->nama_ibu_kandung : 0,
							'kerja_ayah' 		=> ($data->pekerjaan_id_ayah) ? $data->pekerjaan_id_ayah : 1,
							'kerja_ibu' 		=> ($data->pekerjaan_id_ibu) ? $data->pekerjaan_id_ibu : 1,
							'nama_wali' 		=> ($data->nama_wali) ? $data->nama_wali : 0,
							'alamat_wali' 		=> ($data->alamat_jalan) ? $data->alamat_jalan : 0,
							'telp_wali' 		=> ($data->nomor_telepon_seluler) ? $data->nomor_telepon_seluler : 0,
							'kerja_wali' 		=> ($data->pekerjaan_id_wali) ? $data->pekerjaan_id_wali : 1,
							'photo' 			=> 0,
							'active' 			=> 1,
							'siswa_id_dapodik' 	=> $data->peserta_didik_id,
							'last_sync'			=> date('Y-m-d H:i:s'),
						);
						$password = 12345678;
						$find_rombel = App\Rombongan_belajar::where('rombel_id_dapodik', '=', $data->rombongan_belajar_id)->first();//->where('semester_id', '=', $semester->semester_id)
						$find_data_siswa = App\Siswa::where('siswa_id_dapodik', '=', $data->peserta_didik_id)->first();
						if($find_data_siswa){
							$find_user = App\User::where('siswa_id', '=', $find_data_siswa->siswa_id)->first();
							if(!$find_user){
								$create_user = App\User::create([
									'name' => $data->nama,
									'email' => $data->email,
									'nisn'	=> $data->nisn,
									'password' => Hash::make($password),
									'last_sync'	=> date('Y-m-d H:i:s'),
									'sekolah_id'	=> $sekolah->sekolah_id,
									'password_dapo'	=> md5($password),
									'siswa_id'	=> $find_data_siswa->siswa_id,
								]);
								if($create_user){
									$adminRole = App\Role::where('name', 'siswa')->first();
									$CheckadminRole = DB::table('role_user')->where('user_id', $create_user->user_id)->first();
									if(!$CheckadminRole){
										$create_user->attachRole($adminRole);
									}
								}
							}
							$attributes_update_anggota_rombel = array(
								'sekolah_id'				=> $sekolah->sekolah_id,
								'semester_id' 				=> $semester->semester_id, 
								'rombongan_belajar_id' 		=> $find_rombel->rombongan_belajar_id, 
								'siswa_id' 					=> $find_data_siswa->siswa_id,
								'anggota_rombel_id_dapodik'	=> $data->anggota_rombel_id,
								'last_sync'			=> date('Y-m-d H:i:s'),
							);
							$find_anggota_rombel = App\Anggota_rombel::where('anggota_rombel_id_dapodik', $data->anggota_rombel_id)->first();//->where('semester_id', '=', $semester->semester_id)
							if($find_anggota_rombel){
								App\Anggota_rombel::find($find_anggota_rombel->anggota_rombel_id)->update($attributes_update_anggota_rombel);
								$result = 'update';
								$class = 'table-danger"';
							} else {
								$dataguru = App\Anggota_rombel::create($attributes_update_anggota_rombel);
								$result = 'insert anggota';
								$class = 'table-warning"';
							}
						} else {
							$create_siswa = App\Siswa::create($insert_siswa);
							$find_user = App\User::where('siswa_id', '=', $create_siswa->siswa_id)->first();
							if(!$find_user){
								$create_user = App\User::create([
									'name' => $data->nama,
									'email' => $data->email,
									'nisn'	=> $data->nisn,
									'password' => Hash::make($password),
									'last_sync'	=> date('Y-m-d H:i:s'),
									'sekolah_id'	=> $sekolah->sekolah_id,
									'password_dapo'	=> md5($password),
									'siswa_id'	=> $create_siswa->siswa_id,
								]);
								if($create_user){
									$adminRole = App\Role::where('name', 'siswa')->first();
									$CheckadminRole = DB::table('role_user')->where('user_id', $create_user->user_id)->first();
									if(!$CheckadminRole){
										$create_user->attachRole($adminRole);
									}
								}
							}
							$attributes_update_anggota_rombel = array(
								'sekolah_id'				=> $sekolah->sekolah_id,
								'semester_id' 				=> $semester->semester_id, 
								'rombongan_belajar_id' 		=> $find_rombel->rombongan_belajar_id, 
								'siswa_id' 					=> $create_siswa->siswa_id,
								'anggota_rombel_id_dapodik'	=> $data->anggota_rombel_id,
								'last_sync'			=> date('Y-m-d H:i:s'),
							);
							$find_anggota_rombel = App\Anggota_rombel::where('anggota_rombel_id_dapodik', $data->anggota_rombel_id)->first();//->where('semester_id', '=', $semester->semester_id)
							if($find_anggota_rombel){
								App\Anggota_rombel::find($find_anggota_rombel->anggota_rombel_id)->update($attributes_update_anggota_rombel);
							} else {
								App\Anggota_rombel::create($attributes_update_anggota_rombel);
							}
							$result = 'insert';
							$class = 'table-success"';
						}
					//if(!$find_data_siswa){
			?>
				<tr class="<?php echo $class; ?>">
					<td class="text-center"><?php echo $no++; ?></td>
					<td><?php echo $data->nama_siswa; ?></td>
					<td class="text-center"><?php echo date('d-m-Y',strtotime($data->tanggal_lahir)); ?></td>
					<td class="text-center"><?php echo $data->nipd; ?></td>
					<td class="text-center"><?php echo $data->nisn; ?></td>
					<td class="text-center"><?php echo $result; ?></td>
				</tr>
			<?php
			}
			//break; 
			} else { ?>
				<tr>
					<td class="text-center">Tidak ada data untuk ditampilkan</td>
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