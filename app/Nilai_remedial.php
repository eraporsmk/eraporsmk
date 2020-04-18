<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class Nilai_remedial extends Model
{
    use SoftDeletes;
    public $incrementing = false;
	protected $table = 'nilai_remedial';
	protected $primaryKey = 'nilai_remedial_id';
	protected $guarded = [];
	public function pembelajaran(){
		return $this->hasOne('App\Pembelajaran', 'pembelajaran_id', 'pembelajaran_id')->whereNotNull('kelompok_id');
	}
}
