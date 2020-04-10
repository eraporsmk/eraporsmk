<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Traits\Uuid;
use Illuminate\Database\Eloquent\SoftDeletes;
class Anggota_akt_pd extends Model
{
    use Uuid;
	use SoftDeletes;
    public $incrementing = false;
	protected $table = 'anggota_akt_pd';
	protected $primaryKey = 'anggota_akt_pd_id';
	protected $guarded = [];
	public function siswa(){
		return $this->hasOne('App\Siswa', 'peserta_didik_id', 'peserta_didik_id');
	}
}
