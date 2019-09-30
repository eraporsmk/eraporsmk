<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Traits\Uuid;
class Deskripsi_mata_pelajaran extends Model
{
    use Uuid;
    public $incrementing = false;
	protected $table = 'deskripsi_mata_pelajaran';
	protected $primaryKey = 'deskripsi_mata_pelajaran_id';
	protected $guarded = [];
	public function anggota_rombel(){
		return $this->hasOne('App\Anggota_rombel', 'anggota_rombel_id', 'anggota_rombel_id');
	}
	public function pembelajaran(){
		return $this->hasOne('App\Pembelajaran', 'pembelajaran_id', 'pembelajaran_id');
	}
}
