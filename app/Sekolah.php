<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Support\Database\CacheQueryBuilder;
class Sekolah extends Model
{
    public $incrementing = false;
	protected $table = 'sekolah';
	protected $primaryKey = 'sekolah_id';
	protected $guarded = [];
	public function guru(){
		return $this->hasOne('App\Guru', 'guru_id', 'guru_id');
	}
}
