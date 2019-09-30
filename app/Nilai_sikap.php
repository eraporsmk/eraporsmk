<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Traits\Uuid;
use Illuminate\Database\Eloquent\SoftDeletes;
class Nilai_sikap extends Model
{
    use Uuid;
    use SoftDeletes;
    public $incrementing = false;
	protected $table = 'nilai_sikap';
	protected $primaryKey = 'nilai_sikap_id';
	protected $guarded = [];
	public function ref_sikap(){
		return $this->hasOne('App\Sikap', 'sikap_id', 'sikap_id');
	}
	public function anggota_rombel(){
		return $this->hasOne('App\Anggota_rombel', 'anggota_rombel_id', 'anggota_rombel_id');
    }
	public function guru(){
		return $this->hasOne('App\Guru', 'guru_id', 'guru_id');
	}
}
