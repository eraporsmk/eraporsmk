<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Traits\Uuid;
use Illuminate\Database\Eloquent\SoftDeletes;
class Penilaian_ukk extends Model
{
    use Uuid;
    use SoftDeletes;
    public $incrementing = false;
	protected $table = 'penilaian_ukk';
	protected $primaryKey = 'penilaian_ukk_id';
	protected $guarded = [];
}
