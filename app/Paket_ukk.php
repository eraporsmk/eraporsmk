<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Traits\Uuid;
use Illuminate\Database\Eloquent\SoftDeletes;
class Paket_ukk extends Model
{
    use Uuid;
    use SoftDeletes;
    public $incrementing = false;
	protected $table = 'ref.paket_ukk';
	protected $primaryKey = 'paket_ukk_id';
	protected $guarded = [];
	public function jurusan(){
		return $this->hasOne('App\Jurusan', 'jurusan_id', 'jurusan_id');
	}
	public function kurikulum(){
		return $this->hasOne('App\Kurikulum', 'kurikulum_id', 'kurikulum_id');
	}
	public function unit_ukk(){
		return $this->hasMany('App\Unit_ukk', 'paket_ukk_id', 'paket_ukk_id');
	}
}
