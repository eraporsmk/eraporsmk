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
}
