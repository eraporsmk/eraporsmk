<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Tahun_ajaran extends Model
{
    public $incrementing = false;
	protected $table = 'ref.tahun_ajaran';
	protected $primaryKey = 'tahun_ajaran_id';
	protected $guarded = [];
	public function semester(){
		return $this->hasMany('App\Semester', 'tahun_ajaran_id', 'tahun_ajaran_id')->orderBy('semester_id', 'desc');
    }
}
