<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Traits\Uuid;
class Nilai_ekstrakurikuler extends Model
{
    use Uuid;
    public $incrementing = false;
	protected $table = 'nilai_ekstrakurikuler';
	protected $primaryKey = 'nilai_ekstrakurikuler_id';
	protected $guarded = [];
	public function ekstrakurikuler(){
		return $this->hasOne('App\Ekstrakurikuler', 'ekstrakurikuler_id', 'ekstrakurikuler_id');
	}
}
