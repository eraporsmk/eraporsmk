<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Traits\Uuid;
class Gelar_ptk extends Model
{
    use Uuid;
    public $incrementing = false;
	protected $table = 'gelar_ptk';
	protected $primaryKey = 'gelar_ptk_id';
	protected $guarded = [];
	public function gelar(){
		return $this->hasOne('App\Gelar', 'gelar_akademik_id', 'gelar_akademik_id');
	}
}
