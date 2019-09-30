<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Traits\Uuid;
class Absensi extends Model
{
    use Uuid;
    public $incrementing = false;
	protected $table = 'absensi';
	protected $primaryKey = 'absensi_id';
	protected $guarded = [];
	public function anggota_rombel(){
		return $this->hasOne('App\Anggota_rombel', 'anggota_rombel_id', 'anggota_rombel_id');
	}
}
