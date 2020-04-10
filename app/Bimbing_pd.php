<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Traits\Uuid;
use Illuminate\Database\Eloquent\SoftDeletes;
class Bimbing_pd extends Model
{
    use Uuid;
	use SoftDeletes;
    public $incrementing = false;
	protected $table = 'bimbing_pd';
	protected $primaryKey = 'bimbing_pd_id';
	protected $guarded = [];
	public function akt_pd(){
		return $this->hasOne('App\Akt_pd', 'akt_pd_id', 'akt_pd_id');
		/*return $this->hasManyThrough(
            'App\Akt_pd',
            'App\Mou',
            '1', // Foreign key on history table...
            'mou_id', // Foreign key on users table...
            '3', // Local key on suppliers table...
            'mou_id' // Local key on users table...
        );*/
	}
}
