<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Rombongan_belajar_dapodik extends Model
{
    protected $connection = 'dapodik';
	public $incrementing = false;
	protected $table = 'rombongan_belajar';
	protected $primaryKey = 'rombongan_belajar_id';
}
