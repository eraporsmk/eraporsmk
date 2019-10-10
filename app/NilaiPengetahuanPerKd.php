<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class NilaiPengetahuanPerKd extends Model
{
    protected $table = 'view_nilai_pengetahuan_perkd';
	public function siswa(){
        /*return $this->hasOneThrough(
            'App\Siswa',
			'App\Anggota_rombel',
			'rombongan_belajar_id',
			'rombongan_belajar_id',
			'rombongan_belajar_id',
			'rombongan_belajar_id'
        );*/
		return $this->hasOneThrough(
            'App\Siswa',
            'App\Anggota_rombel',
            'siswa_id', // Foreign key on Anggota_rombel table...
            'siswa_id', // Foreign key on Siswa table...
            'anggota_rombel_id', // Local key on NilaiKeterampilanPerKd table...
            'anggota_rombel_id' // Local key on users table...
        );
    }
	public function kd_nilai(){
		return $this->hasOne('App\Kd_nilai', 'kompetensi_dasar_id', 'kompetensi_dasar_id');
	}
}
