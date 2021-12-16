<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Opsi_budaya_kerja extends Model
{
    public $incrementing = false;
	protected $table = 'ref.opsi_budaya_kerja';
	protected $primaryKey = 'opsi_id';
	protected $guarded = [];
}
