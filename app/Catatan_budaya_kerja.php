<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Traits\Uuid;
use Illuminate\Database\Eloquent\SoftDeletes;
class Catatan_budaya_kerja extends Model
{
    use Uuid, SoftDeletes;
    public $incrementing = false;
	protected $table = 'catatan_budaya_kerja';
	protected $primaryKey = 'catatan_budaya_kerja_id';
	protected $guarded = [];
	public function anggota_rombel(){
		return $this->hasOne('App\Anggota_rombel', 'anggota_rombel_id', 'anggota_rombel_id');
	}
}
