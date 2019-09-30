<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Traits\Uuid;
class Mou extends Model
{
    use Uuid;
    public $incrementing = false;
	protected $table = 'mou';
	protected $primaryKey = 'mou_id';
	protected $guarded = [];
	public function dudi(){
		return $this->hasOne('App\Dudi', 'dudi_id', 'dudi_id');
	}
}
