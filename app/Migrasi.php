<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Migrasi extends Model
{
    public $timestamps = false;
    protected $table = 'migrasi';
	protected $primaryKey = 'nama_table';
    protected $guarded = [];
}
