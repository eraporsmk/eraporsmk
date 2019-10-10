<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class NilaiKeterampilanPerKd extends Model
{
    protected $table = 'view_nilai_keterampilan_perkd';
	public function siswa(){
        return $this->hasOneThrough(
            'App\Siswa',
            'App\Anggota_rombel',
            'anggota_rombel_id', 
            'siswa_id', 
            'anggota_rombel_id', 
            'siswa_id' 
        );
    }
	public function kd_nilai(){
		return $this->hasOne('App\Kd_nilai', 'kompetensi_dasar_id', 'kompetensi_dasar_id');
	}
}
