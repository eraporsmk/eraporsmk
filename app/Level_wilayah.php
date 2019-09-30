<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Level_wilayah extends Model
{
    public $incrementing = false;
	protected $table = 'level_wilayah';
	protected $primaryKey = 'id_level_wilayah';
	protected $fillable = [
        'id_level_wilayah', 'level_wilayah', 'last_sync'
    ];
}
