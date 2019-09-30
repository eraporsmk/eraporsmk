<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Traits\Uuid;
class Catatan_wali extends Model
{
    use Uuid;
    public $incrementing = false;
	protected $table = 'catatan_wali';
	protected $primaryKey = 'catatan_wali_id';
	protected $guarded = [];
	public function anggota_rombel(){
		return $this->hasOne('App\Anggota_rombel', 'anggota_rombel_id', 'anggota_rombel_id');
	}
}
