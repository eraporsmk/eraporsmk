<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Traits\Uuid;
use Illuminate\Database\Eloquent\SoftDeletes;
class Akt_pd extends Model
{
    use Uuid;
	use SoftDeletes;
    public $incrementing = false;
	protected $table = 'akt_pd';
	protected $primaryKey = 'akt_pd_id';
	protected $guarded = [];
	public function anggota_akt_pd(){
		return $this->hasMany('App\Anggota_akt_pd', 'akt_pd_id', 'akt_pd_id');
	}
	public function dudi(){
		return $this->hasOneThrough(
            'App\Dudi',
            'App\Mou',
            'mou_id', // Foreign key on users table...
            'dudi_id', // Foreign key on history table...
            'mou_id', // Local key on suppliers table...
            'dudi_id' // Local key on users table...
        );
	}
}
