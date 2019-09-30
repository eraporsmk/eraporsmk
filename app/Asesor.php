<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Traits\Uuid;
class Asesor extends Model
{
    use Uuid;
    public $incrementing = false;
	protected $table = 'asesor';
	protected $primaryKey = 'asesor_id';
	protected $guarded = [];
	public function guru(){
		return $this->hasOne('App\Guru', 'guru_id', 'guru_id');
	}
	public function dudi(){
		return $this->hasOne('App\Dudi', 'dudi_id', 'dudi_id');
	}
}
