<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Sikap extends Model
{
    protected $table = 'ref.sikap';
	protected $primaryKey = 'sikap_id';
	protected $guarded = [];
	public function sikap(){
		return $this->hasMany('App\Sikap', 'sikap_induk', 'sikap_id');
	}
}
