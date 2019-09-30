<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Agama extends Model
{
    protected $table = 'ref.agama';
	protected $fillable = [
        'nama', 'last_sync'
    ];
}
