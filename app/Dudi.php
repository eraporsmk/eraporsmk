<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Traits\Uuid;
class Dudi extends Model
{
    use Uuid;
    public $incrementing = false;
	protected $table = 'dudi';
	protected $primaryKey = 'dudi_id';
	protected $guarded = [];
	public function kecamatan(){
		return $this->hasOne('App\Mst_wilayah', 'kode_wilayah', 'kode_wilayah');
    }
}
