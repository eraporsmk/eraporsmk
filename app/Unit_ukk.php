<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Traits\Uuid;
use Illuminate\Database\Eloquent\SoftDeletes;
class Unit_ukk extends Model
{
    use Uuid;
    use SoftDeletes;
    public $incrementing = false;
	protected $table = 'unit_ukk';
	protected $primaryKey = 'unit_ukk_id';
	protected $guarded = [];
}
