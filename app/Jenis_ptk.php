<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Jenis_ptk extends Model
{
    public $incrementing = false;
	protected $table = 'ref.jenis_ptk';
	protected $primaryKey = 'jenis_ptk_id';
	protected $guarded = [];
}
