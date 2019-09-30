<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Traits\Uuid;
use Illuminate\Database\Eloquent\SoftDeletes;
class Bobot_keterampilan extends Model
{
    use Uuid;
	use SoftDeletes;
    public $incrementing = false;
	protected $table = 'bobot_keterampilan';
	protected $primaryKey = 'bobot_keterampilan_id';
	protected $guarded = [];
	public function pembelajaran(){
		//$semester = HelperServiceProvider::get_ta();
        //return $this->hasMany('App\Pembelajaran', 'rombongan_belajar_id', 'rombongan_belajar_id')->where('semester_id', '=', $semester->semester_id);
		return $this->hasOne('App\Pembelajaran', 'pembelajaran_id', 'pembelajaran_id');//->whereNotNull('kelompok_id')->whereNotNull('no_urut');
    }
	public function metode(){
		//$semester = HelperServiceProvider::get_ta();
        //return $this->hasMany('App\Pembelajaran', 'rombongan_belajar_id', 'rombongan_belajar_id')->where('semester_id', '=', $semester->semester_id);
		return $this->hasOne('App\Teknik_penilaian', 'teknik_penilaian_id', 'metode_id');//->whereNotNull('kelompok_id')->whereNotNull('no_urut');
    }
}
