<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Budaya_kerja extends Model
{
    public $incrementing = false;
	protected $table = 'ref.budaya_kerja';
	protected $primaryKey = 'budaya_kerja_id';
	protected $guarded = [];
    public function elemen_budaya_kerja(){
        return $this->hasMany(Elemen_budaya_kerja::class, 'budaya_kerja_id', 'budaya_kerja_id');
    }
}
