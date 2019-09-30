<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Traits\Uuid;
class Catatan_ppk extends Model
{
    use Uuid;
    public $incrementing = false;
	protected $table = 'catatan_ppk';
	protected $primaryKey = 'catatan_ppk_id';
	protected $guarded = [];
	public function anggota_rombel(){
		return $this->hasOne('App\Anggota_rombel', 'anggota_rombel_id', 'anggota_rombel_id');
	}
	public function nilai_karakter(){
		return $this->hasMany('App\Nilai_karakter', 'catatan_ppk_id', 'catatan_ppk_id');
	}
	public function siswa(){
		return $this->hasOneThrough(
            'App\Siswa',
            'App\Anggota_rombel',
            'anggota_rombel_id',
            'peserta_didik_id',
            'anggota_rombel_id',
            'peserta_didik_id'
        );
	}
}
