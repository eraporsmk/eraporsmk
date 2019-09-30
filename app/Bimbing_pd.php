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
}
