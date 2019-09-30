<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Kompetensi_dasar extends Model
{
    protected $table = 'ref.kompetensi_dasar';
	protected $guarded = [];
	public function mata_pelajaran(){
		return $this->hasOne('App\Mata_pelajaran', 'mata_pelajaran_id', 'mata_pelajaran_id');
	}
	public function pembelajaran(){
		return $this->hasOne('App\Pembelajaran', 'mata_pelajaran_id', 'mata_pelajaran_id');
	}
}
