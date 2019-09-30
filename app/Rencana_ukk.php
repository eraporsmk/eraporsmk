<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Traits\Uuid;
use Illuminate\Database\Eloquent\SoftDeletes;
class Rencana_ukk extends Model
{
	use Uuid;
    use SoftDeletes;
    public $incrementing = false;
	protected $table = 'rencana_ukk';
	protected $primaryKey = 'rencana_ukk_id';
	protected $guarded = [];
	public function guru_internal(){
		return $this->hasOne('App\Guru', 'guru_id', 'internal');
	}
	public function guru_eksternal(){
		return $this->hasOne('App\Guru', 'guru_id', 'eksternal')->with('dudi');
	}
	public function paket_ukk(){
		return $this->hasOne('App\Paket_ukk', 'paket_ukk_id', 'paket_ukk_id');
	}
	public function nilai_ukk(){
		return $this->hasOne('App\Nilai_ukk', 'rencana_ukk_id', 'rencana_ukk_id');
	}
}
