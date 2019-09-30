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
	@if ($data)
		@if ($data->post_login == 1)
		<table class="table table-bordered table-striped table-hover">
            <thead>
				<tr>
					<th class="text-center">Data</th>
					<th class="text-center">Status</th>
					<th class="text-center">Jml Data Dapodik</th>
					<th class="text-center">Jml Data Erapor</th>
					<th class="text-center">Jml Data Sudah Tersinkronisasi</th>
					<th class="text-center">Aksi</th>
	            </tr>
            </thead>
			<tbody>
			<?php
			$sekolah_erapor = App\Sekolah::where('sekolah_id', '447da28d-b210-4afa-b24e-4e9416d4671e')->first();
			//$this->sekolah->find_all("sekolah_id = '$id_sekolah_dapodik'");
			$get_sekolah_sinkron = App\Sekolah::find($data->sekolah_id);
			$sekolah_erapor = count($sekolah_erapor);
			$sekolah_sinkron = count($get_sekolah_sinkron);
			$ptk_terdaftar = $data->ptk_terdaftar;
			$guru_erapor = App\Guru::where('sekolah_id', '=', $data->sekolah_id)->count();
			//$guru_sinkron = App\Guru::where([['sekolah_id', '=', $data->sekolah_id], ['guru_id_dapodik', '<>', '']])->count();
			$guru_sinkron = App\Guru::where('sekolah_id', '=', $data->sekolah_id)->whereNotNull('guru_id_dapodik')->count();
 			$rombongan_belajar = $data->rombongan_belajar;
			$rombel_erapor = App\Rombongan_belajar::where('sekolah_id', '=', $data->sekolah_id)->where('semester_id', '=', $semester->semester_id)->where('jenis_rombel', '=', 1)->count();
			$rombel_sinkron = App\Rombongan_belajar::where('sekolah_id', '=', $data->sekolah_id)->where('semester_id', '=', $semester->semester_id)->where('jenis_rombel', '=', 1)->count();
			$registrasi_peserta_didik = $data->registrasi_peserta_didik;
			$siswa_erapor = DB::table('anggota_rombel')->join('rombongan_belajar', function ($join) {
				$join->on('anggota_rombel.rombongan_belajar_id', '=', 'rombongan_belajar.rombongan_belajar_id')->where('rombongan_belajar.jenis_rombel', '=', 1);
			})->where('anggota_rombel.sekolah_id', '=', $data->sekolah_id)->where('anggota_rombel.semester_id', '=', $semester->semester_id)->whereNull('anggota_rombel.deleted_at')->count();
			$siswa_sinkron = DB::table('anggota_rombel')->join('rombongan_belajar', function ($join) {
				$join->on('anggota_rombel.rombongan_belajar_id', '=', 'rombongan_belajar.rombongan_belajar_id')->where('rombongan_belajar.jenis_rombel', '=', 1);
			})->where('anggota_rombel.sekolah_id', '=', $data->sekolah_id)->where('anggota_rombel.semester_id', '=', $semester->semester_id)->whereNull('anggota_rombel.deleted_at')->count();
			$siswa_keluar_dapodik = $data->siswa_keluar_dapodik;
			$siswa_keluar_erapor = DB::table('anggota_rombel')->join('rombongan_belajar', function ($join) {
				$join->on('anggota_rombel.rombongan_belajar_id', '=', 'rombongan_belajar.rombongan_belajar_id')->where('rombongan_belajar.jenis_rombel', '=', 1);
			})->where('anggota_rombel.sekolah_id', '=', $data->sekolah_id)->where('anggota_rombel.semester_id', '=', $semester->semester_id)->whereNotNull('anggota_rombel.deleted_at')->count();
			$siswa_keluar_sinkron = DB::table('anggota_rombel')->join('rombongan_belajar', function ($join) {
				$join->on('anggota_rombel.rombongan_belajar_id', '=', 'rombongan_belajar.rombongan_belajar_id')->where('rombongan_belajar.jenis_rombel', '=', 1);
			})->where('anggota_rombel.sekolah_id', '=', $data->sekolah_id)->where('anggota_rombel.semester_id', '=', $semester->semester_id)->whereNotNull('anggota_rombel.deleted_at')->count();
			$pembelajaran_dapodik = $data->pembelajaran_dapodik;
			$pembelajaran_erapor = App\Pembelajaran::where('sekolah_id', '=', $data->sekolah_id)->where('semester_id', '=', $semester->semester_id)->count();
			$pembelajaran_sinkron = App\Pembelajaran::where('sekolah_id', '=', $data->sekolah_id)->where('semester_id', '=', $semester->semester_id)->whereNotNull('pembelajaran_id_dapodik')->count();
			$ekskul_dapodik = $data->ekskul_dapodik;
			$ekskul_erapor = App\Ekstrakurikuler::where('sekolah_id', '=', $data->sekolah_id)->where('semester_id', '=', $semester->semester_id)->count();
			$ekskul_sinkron = App\Ekstrakurikuler::where('sekolah_id', '=', $data->sekolah_id)->where('semester_id', '=', $semester->semester_id)->whereNotNull('id_kelas_ekskul')->count();
			$anggota_ekskul_dapodik = $data->anggota_ekskul_dapodik;
			$anggota_ekskul_erapor = DB::table('anggota_rombel')->join('rombongan_belajar', function ($join) {
				$join->on('anggota_rombel.rombongan_belajar_id', '=', 'rombongan_belajar.rombongan_belajar_id')->where('rombongan_belajar.jenis_rombel', '=', 51);
			})->where('anggota_rombel.sekolah_id', '=', $data->sekolah_id)->where('anggota_rombel.semester_id', '=', $semester->semester_id)->whereNull('anggota_rombel.deleted_at')->count();
			$anggota_ekskul_sinkron = DB::table('anggota_rombel')->join('rombongan_belajar', function ($join) {
				$join->on('anggota_rombel.rombongan_belajar_id', '=', 'rombongan_belajar.rombongan_belajar_id')->where('rombongan_belajar.jenis_rombel', '=', 51);
			})->where('anggota_rombel.sekolah_id', '=', $data->sekolah_id)->where('anggota_rombel.semester_id', '=', $semester->semester_id)->whereNull('anggota_rombel.deleted_at')->count();
			$dudi_dapodik = $data->dudi_dapodik;
			$dudi_erapor = App\Dudi::where('sekolah_id', '=', $data->sekolah_id)->count();
			$dudi_sinkron = App\Dudi::where('sekolah_id', '=', $data->sekolah_id)->count();
			$jurusan = $data->jurusan;
			$jurusan_erapor = App\Jurusan::count();
			$jurusan_sinkron = App\Jurusan::count();
			$kurikulum = $data->kurikulum;
			$kurikulum_erapor = App\Kurikulum::count();
			$kurikulum_sinkron = App\Kurikulum::count();
			$mata_pelajaran = $data->mata_pelajaran;
			$mata_pelajaran_erapor = App\Mata_pelajaran::count();
			$mata_pelajaran_sinkron = App\Mata_pelajaran::count();
			$mata_pelajaran_kurikulum = $data->mata_pelajaran_kurikulum;
			$mata_pelajaran_kurikulum_erapor = App\Mata_pelajaran_kurikulum::count();
			$mata_pelajaran_kurikulum_sinkron = App\Mata_pelajaran_kurikulum::count();
			$kompetensi_dasar = $data->ref_kd;
			$kompetensi_dasar_erapor = App\Kompetensi_dasar::count();
			$kompetensi_dasar_sinkron = App\Kompetensi_dasar::whereNull('user_id')->count();
			$make_array = array(
				0 => 
					array(
						'nama' => 'Sekolah',
						'link' => 'sekolah',
						'get_dapodik' => 1,
						'get_erapor' => $sekolah_erapor,
						'get_sinkron' => $sekolah_sinkron,
						'class' => 'count_sekolah',
					), 
				1 => 
					array(
						'nama' => 'PTK',
						'link' => 'guru',
						'get_dapodik' => $ptk_terdaftar,
						'get_erapor' => $guru_erapor,
						'get_sinkron' => $guru_sinkron,
						'class' => 'count_guru',
					), 
				2 => 
					array(
						'nama' => 'Rombongan Belajar',
						'link' => 'rombongan-belajar',
						'get_dapodik' => $rombongan_belajar,
						'get_erapor' => $rombel_erapor,
						'get_sinkron' => $rombel_sinkron,
						'class' => 'count_rombel',
					), 
				3 => 
					array(
						'nama' => 'Peserta Didik Aktif',
						'link' => 'siswa-aktif',
						'get_dapodik' => $registrasi_peserta_didik,
						'get_erapor' => $siswa_erapor,
						'get_sinkron' => $siswa_sinkron,
						'class' => 'count_siswa',
					), 
				4 => 
					array(
						'nama' => 'Peserta Didik Keluar',
						'link' => 'siswa-keluar',
						'get_dapodik' => $siswa_keluar_dapodik,
						'get_erapor' => $siswa_keluar_erapor,
						'get_sinkron' => $siswa_keluar_sinkron,
						'class' => 'count_siswa',
					), 
				5 => 
					array(
						'nama' => 'Pembelajaran',
						'link' => 'pembelajaran',
						'get_dapodik' => $pembelajaran_dapodik,
						'get_erapor' => $pembelajaran_erapor,
						'get_sinkron' => $pembelajaran_sinkron,
						'class' => 'count_pembelajaran',
					),
				6 => 
					array(
						'nama' => 'Ekstrakurikuler',
						'link' => 'ekskul',
						'get_dapodik' => $ekskul_dapodik,
						'get_erapor' => $ekskul_erapor,
						'get_sinkron' => $ekskul_sinkron,
						'class' => 'count_ekskul',
					),
				7 => 
					array(
						'nama' => 'Anggota Ekstrakurikuler',
						'link' => 'anggota-ekskul',
						'get_dapodik' => $anggota_ekskul_dapodik,
						'get_erapor' => $anggota_ekskul_erapor,
						'get_sinkron' => $anggota_ekskul_sinkron,
						'class' => 'count_ekskul',
					),
				8 => 
					array(
						'nama' => 'Relasi Dunia Usaha &amp; Industri',
						'link' => 'dudi',
						'get_dapodik' => $dudi_dapodik,
						'get_erapor' => $dudi_erapor,
						'get_sinkron' => $dudi_sinkron,
						'class' => 'count_dudi',
					),
				9 => 
					array(
						'nama' => 'Jurusan',
						'link' => 'jurusan',
						'get_dapodik' => $jurusan,
						'get_erapor' => $jurusan_erapor,
						'get_sinkron' => $jurusan_sinkron,
						'class' => 'count_ekskul',
					),
				10 => 
					array(
						'nama' => 'Kurikulum',
						'link' => 'kurikulum',
						'get_dapodik' => $kurikulum,
						'get_erapor' => $kurikulum_erapor,
						'get_sinkron' => $kurikulum_sinkron,
						'class' => 'count_ekskul',
					),
				11 => 
					array(
						'nama' => 'Mata Pelajaran',
						'link' => 'mata-pelajaran',
						'get_dapodik' => $mata_pelajaran,
						'get_erapor' => $mata_pelajaran_erapor,
						'get_sinkron' => $mata_pelajaran_sinkron,
						'class' => 'count_ekskul',
					),
				12 => 
					array(
						'nama' => 'Mata Pelajaran Kurikulum',
						'link' => 'mapel-kur',
						'get_dapodik' => $mata_pelajaran_kurikulum,
						'get_erapor' => $mata_pelajaran_kurikulum_erapor,
						'get_sinkron' => $mata_pelajaran_kurikulum_sinkron,
						'class' => 'count_ekskul',
					),
				13 => 
					array(
						'nama' => 'Ref. Kompetensi Dasar',
						'link' => 'ref-kd',
						'get_dapodik' => $kompetensi_dasar,
						'get_erapor' => $kompetensi_dasar_erapor,
						'get_sinkron' => $kompetensi_dasar_sinkron,
						'class' => 'count_ref_kd',
					),
			);
			foreach($make_array as $d){
				if($d['get_sinkron']){
					$status = 'Lengkap';
					$btn = 'btn-danger';
					$text = 'Update';
					if($d['get_dapodik'] > $d['get_sinkron']){
						$status = 'Kurang';
						$btn = 'btn-warning';
						$text = 'Sinkron Ulang';
					}
					if($d['link'] == 'sekolah'){
						if($get_sekolah_sinkron){
							if(!$get_sekolah_sinkron->sinkron){
								$status = '<small class="label bg-red">Sinkron Ulang</small>';
								$btn = 'btn-warning';
								$text = 'Sinkron Ulang';
							}
						}
					}
				} else {
					$status = 'Belum';
					$btn = 'btn-success';
					$text = 'Sinkron';
				}
				if($d['link'] == 'mata_pelajaran' || $d['link'] == 'jurusan'){
					$id_sekolah_dapodik = '';
				}
			?>
				<tr>
					<td><?php echo $d['nama']; ?></td>
					<td class="text-center"><?php echo $status; ?></td>
					<td class="text-center"><?php echo $d['get_dapodik']; ?></td>
					<td class="text-center"><?php echo $d['get_erapor']; ?></td>
					<td class="text-center <?php echo $d['class']; ?>"><?php echo $d['get_sinkron']; ?></td>
					<td class="text-center"><a href="<?php echo url('sinkronisasi/'.$d['link']); ?>" class="<?php echo $d['class']; ?> btn <?php echo $btn; ?> btn-block"><?php echo $text; ?></a></td>
				</tr>
			<?php } ?>
			</tbody>
		</table>
		@else
		<div class="callout callout-danger lead">Anda terhubung ke server direktorat.<br />{{ $data->message }}</div>
		@endif
	@else
	<div class="callout callout-danger lead">Anda tidak terhubung ke server direktorat.<br />Pastikan PC/Laptop Anda terhubung ke internet!</div>
	@endif
@stop