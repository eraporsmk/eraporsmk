<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Traits\Uuid;
class Kd_json extends Model
{
    use Uuid;
    public $incrementing = false;
	protected $table = 'kd_json';
	protected $primaryKey = 'kd_json_id';
	protected $guarded = [];
}
