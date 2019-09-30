<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Kurikulum extends Model
{
    public $incrementing = false;
	protected $table = 'ref.kurikulum';
	protected $primaryKey = 'kurikulum_id';
	protected $guarded = [];
	public function kurikulum(){
		return $this->hasMany('App\Rombongan_belajar', 'kurikulum_id', 'kurikulum_id');
    }
}
