<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Traits\Uuid;
use Illuminate\Database\Eloquent\SoftDeletes;
class Teknik_penilaian extends Model
{
    use Uuid;
	use SoftDeletes;
    public $incrementing = false;
	protected $table = 'teknik_penilaian';
	protected $primaryKey = 'teknik_penilaian_id';
	protected $guarded = [];
}
