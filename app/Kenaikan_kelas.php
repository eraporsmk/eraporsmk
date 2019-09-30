<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Traits\Uuid;
class Kenaikan_kelas extends Model
{
    use Uuid;
    public $incrementing = false;
	protected $table = 'kenaikan_kelas';
	protected $primaryKey = 'kenaikan_kelas_id';
	protected $guarded = [];
	public function anggota_rombel(){
		return $this->hasOne('App\Anggota_rombel', 'anggota_rombel_id', 'anggota_rombel_id');
	}
	public function rombongan_belajar(){
		return $this->hasOne('App\Rombongan_belajar', 'rombongan_belajar_id', 'rombongan_belajar_id');
	}
}
