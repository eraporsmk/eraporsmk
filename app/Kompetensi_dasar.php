<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
//use App\Traits\Uuid;
use Illuminate\Database\Eloquent\SoftDeletes;
class Kompetensi_dasar extends Model
{
    //use Uuid;
	use SoftDeletes;
    public $incrementing = false;
	protected $table = 'ref.kompetensi_dasar';
	protected $primaryKey = 'kompetensi_dasar_id';
	protected $guarded = [];
	public function mata_pelajaran(){
		return $this->hasOne(Mata_pelajaran::class, 'mata_pelajaran_id', 'mata_pelajaran_id');
	}
	public function pembelajaran(){
		return $this->hasOne(Pembelajaran::class, 'mata_pelajaran_id', 'mata_pelajaran_id');
	}
}
