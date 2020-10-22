<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Traits\Uuid;
class Status_penilaian extends Model
{
	use Uuid;
    public $incrementing = false;
	protected $table = 'status_penilaian';
	protected $primaryKey = 'status_penilaian_id';
	protected $guarded = [];
}
