<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Negara extends Model
{
    public $incrementing = false;
	protected $table = 'negara';
	protected $primaryKey = 'negara_id';
	protected $fillable = [
        'negara_id', 'nama', 'luar_negeri', 'last_sync'
    ];
}
