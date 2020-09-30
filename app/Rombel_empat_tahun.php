<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Rombel_empat_tahun extends Model
{
    public $incrementing = false;
	protected $table = 'rombel_4_tahun';
	protected $primaryKey = 'rombongan_belajar_id';
	protected $guarded = [];
}
