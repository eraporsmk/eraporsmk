<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Traits\Uuid;
use Illuminate\Database\Eloquent\SoftDeletes;
class Nilai_budaya_kerja extends Model
{
    use Uuid;
    use SoftDeletes;
    public $incrementing = false;
	protected $table = 'nilai_budaya_kerja';
	protected $primaryKey = 'nilai_budaya_kerja_id';
	protected $guarded = [];
	public function aspek_budaya_kerja()
    {
        return $this->belongsTo(Aspek_budaya_kerja::class, 'aspek_budaya_kerja_id', 'aspek_budaya_kerja_id');
    }
    public function budaya_kerja(){
        return $this->hasOneThrough(
            'App\Budaya_kerja',
            'App\Aspek_budaya_kerja',
            'aspek_budaya_kerja_id', // Foreign key on users table...
            'budaya_kerja_id', // Foreign key on history table...
            'aspek_budaya_kerja_id', // Local key on suppliers table...
            'budaya_kerja_id' // Local key on users table...
        );
    }
    public function rencana_budaya_kerja(){
        return $this->hasOneThrough(
            'App\Rencana_budaya_kerja',
            'App\Aspek_budaya_kerja',
            'aspek_budaya_kerja_id', // Foreign key on users table...
            'rencana_budaya_kerja_id', // Foreign key on history table...
            'aspek_budaya_kerja_id', // Local key on suppliers table...
            'rencana_budaya_kerja_id' // Local key on users table...
        );
    }
}
