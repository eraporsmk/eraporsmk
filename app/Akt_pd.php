<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Traits\Uuid;
use Illuminate\Database\Eloquent\SoftDeletes;
class Akt_pd extends Model
{
    use Uuid;
	use SoftDeletes;
    public $incrementing = false;
	protected $table = 'akt_pd';
	protected $primaryKey = 'akt_pd_id';
	protected $guarded = [];
}
