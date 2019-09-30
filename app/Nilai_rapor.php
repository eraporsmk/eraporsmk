<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Traits\Uuid;
class Nilai_rapor extends Model
{
    use Uuid;
	use \Staudenmeir\EloquentEagerLimit\HasEagerLimit;
    public $incrementing = false;
	protected $table = 'nilai_rapor';
	protected $primaryKey = 'nilai_rapor_id';
	protected $guarded = [];
	public function pembelajaran(){
		return $this->hasOne('App\Pembelajaran', 'pembelajaran_id', 'pembelajaran_id');
	}
}
