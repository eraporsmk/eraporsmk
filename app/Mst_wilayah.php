<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Mst_wilayah extends Model
{
    public $incrementing = false;
	protected $table = 'ref.mst_wilayah';
	protected $primaryKey = 'kode_wilayah';
	protected $guarded = [];
	public function get_kabupaten(){
		return $this->hasOne('App\Mst_wilayah', 'kode_wilayah', 'mst_kode_wilayah');
    }
}
