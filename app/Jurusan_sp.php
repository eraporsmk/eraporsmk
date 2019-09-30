<?php

namespace App;
use App\Traits\Uuid;
use Illuminate\Database\Eloquent\Model;
class Jurusan_sp extends Model
{
	use Uuid;
    public $incrementing = false;
	protected $table = 'jurusan_sp';
	protected $primaryKey = 'jurusan_sp_id';
	protected $guarded = [];
}
