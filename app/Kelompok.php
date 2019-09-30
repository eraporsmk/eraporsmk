<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Kelompok extends Model
{
    protected $table = 'ref.kelompok';
	protected $primaryKey = 'kelompok_id';
	protected $guarded = [];
}
