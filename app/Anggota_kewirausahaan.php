<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Traits\Uuid;
class Anggota_kewirausahaan extends Model
{
    use Uuid;
    public $incrementing = false;
	protected $table = 'anggota_kewirausahaan';
	protected $primaryKey = 'anggota_kewirausahaan_id';
    protected $guarded = [];
    public function kewirausahaan(){
        return $this->hasOne('App\Kewirausahaan', 'kewirausahaan_id', 'kewirausahaan_id');
    }
    public function anggota_rombel(){
		return $this->hasOne('App\Anggota_rombel', 'anggota_rombel_id', 'anggota_rombel_id');
    }
	public function siswa(){
		return $this->hasOneThrough(
            'App\Anggota_rombel',
            'App\Siswa',
            'peserta_didik_id',
            'peserta_didik_id',
            'anggota_rombel_id',
            'peserta_didik_id'
        );
	}
}
