<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Traits\Uuid;
use Illuminate\Database\Eloquent\SoftDeletes;
class Nilai extends Model
{
    use Uuid;
    use SoftDeletes;
    public $incrementing = false;
	protected $table = 'nilai';
	protected $primaryKey = 'nilai_id';
	protected $guarded = [];
	public function kd_nilai(){
        return $this->hasMany('App\Kd_nilai', 'rencana_penilaian_id', 'rencana_penilaian_id');
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
