<?php
return [
	//'url_server' => 'http://app.erapor:8002/api/',
	'url_server' => 'http://app.erapor-smk.net/api/',
	'table_sync' => [
		'sekolah', //no semester
		'jurusan_sp', //no semester
		'guru', //no semester
		'rombongan_belajar',
		'peserta_didik', //no semester
		'pembelajaran',
		'ekstrakurikuler',
		'anggota_rombel',
		'dudi',
		'mou',
		'absensi',
		'catatan_ppk',
		'catatan_wali',
		'deskripsi_mata_pelajaran',
		'deskripsi_sikap',
		'kd_nilai', //no semester
		'nilai',
		'nilai_akhir',
		'nilai_ekstrakurikuler',
		'nilai_sikap',
		'nilai_ukk',
		'prakerin',
		'prestasi',
		'nilai_remedial',
		'rencana_penilaian',
		'teknik_penilaian', //no semester
		'bobot_keterampilan',
		'nilai_rapor',
		'kenaikan_kelas',
		'kewirausahaan',
		'anggota_kewirausahaan', //no semester
		'ref.kompetensi_dasar', //no semester
		'nilai_un',
		'nilai_us',
		'users',
		'role_user',
	],
	'url_register' => 'https://data.dikdasmen.kemdikbud.go.id/sso/auth/?response_type=code&client_id=ssoSMK&state=300300&redirect_uri=http://103.40.55.249/Do/SSODPD',
	'api_key' => '0b6743d7b41f2fb616058e49650269a42972fb5a69a9180741a3fe2a6f19fc46',
	'access_update' => env('ACCESS_UPDATE', true),
];