<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Traits\Uuid;
class Nilai_karakter extends Model
{
    use Uuid;
    public $incrementing = false;
	protected $table = 'nilai_karakter';
	protected $primaryKey = 'nilai_karakter_id';
	protected $guarded = [];
	public function sikap(){
		return $this->hasOne('App\Sikap', 'sikap_id', 'sikap_id');
	}
}
