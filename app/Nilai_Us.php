<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Traits\Uuid;
use Illuminate\Database\Eloquent\SoftDeletes;
class Nilai_Us extends Model
{
    use Uuid;
    use SoftDeletes;
    public $incrementing = false;
	protected $table = 'nilai_us';
	protected $primaryKey = 'nilai_us_id';
	protected $guarded = [];
	public function siswa(){
		return $this->hasOneThrough(
            'App\Anggota_rombel',
            'App\Siswa',
            'peserta_didik_id',
            'peserta_didik_id',
            'anggota_rombel_id',
            'peserta_didik_id'
        );
	}
}
