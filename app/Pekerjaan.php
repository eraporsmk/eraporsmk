<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Pekerjaan extends Model
{
    public $incrementing = false;
	protected $table = 'ref.pekerjaan';
	protected $primaryKey = 'pekerjaan_id';
	protected $guarded = [];
}
