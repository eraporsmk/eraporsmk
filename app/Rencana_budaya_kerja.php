<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Traits\Uuid;
class Rencana_budaya_kerja extends Model
{
    use Uuid;
    public $incrementing = false;
	protected $table = 'rencana_budaya_kerja';
	protected $primaryKey = 'rencana_budaya_kerja_id';
	protected $guarded = [];
	
	public function aspek_budaya_kerja(){
		return $this->hasMany(Aspek_budaya_kerja::class, 'rencana_budaya_kerja_id', 'rencana_budaya_kerja_id');
	}
	public function rombongan_belajar(){
		return $this->belongsTo(Rombongan_belajar::class, 'rombongan_belajar_id', 'rombongan_belajar_id');
	}
}
