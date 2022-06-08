<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Traits\Uuid;
class Aspek_budaya_kerja extends Model
{
    use Uuid;
    public $incrementing = false;
	protected $table = 'aspek_budaya_kerja';
	protected $primaryKey = 'aspek_budaya_kerja_id';
	protected $guarded = [];
    
    public function rencana_budaya_kerja(){
        return $this->belongsTo(Rencana_budaya_kerja::class, 'rencana_budaya_kerja_id', 'rencana_budaya_kerja_id');
    }
    public function budaya_kerja(){
        return $this->belongsTo(Budaya_kerja::class, 'budaya_kerja_id', 'budaya_kerja_id');
    }
    public function nilai_budaya_kerja(){
        return $this->hasMany(Nilai_budaya_kerja::class, 'aspek_budaya_kerja_id', 'aspek_budaya_kerja_id');
    }
}
