<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Elemen_budaya_kerja extends Model
{
    public $incrementing = false;
	protected $table = 'ref.elemen_budaya_kerja';
	protected $primaryKey = 'elemen_id';
	protected $guarded = [];
    public function budaya_kerja(){
        return $this->belongsTo(Budaya_kerja::class, 'budaya_kerja_id', 'budaya_kerja_id');
    }
    public function nilai_budaya_kerja(){
        return $this->hasOne(Nilai_budaya_kerja::class, 'elemen_id', 'elemen_id');
    }
}
