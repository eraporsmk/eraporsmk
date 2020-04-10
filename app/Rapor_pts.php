<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Traits\Uuid;
class Rapor_pts extends Model
{
    use Uuid;
    public $incrementing = false;
	protected $table = 'rapor_pts';
	protected $primaryKey = 'rapor_pts_id';
	protected $guarded = [];
	public function pembelajaran(){
		return $this->hasMany('App\Pembelajaran', 'pembelajaran_id', 'pembelajaran_id');
	}
	public function rencana_penilaian(){
		return $this->hasOne('App\Rencana_penilaian', 'rencana_penilaian_id', 'rencana_penilaian_id');
	}
	public function nilai(){
		return $this->hasManyThrough(
            'App\Nilai',
            'App\Kd_nilai',
            'rencana_penilaian_id', // Foreign key on history table...
            'kd_nilai_id', // Foreign key on users table...
            'rencana_penilaian_id', // Local key on suppliers table...
            'kd_nilai_id' // Local key on users table...
        );
	}
}
