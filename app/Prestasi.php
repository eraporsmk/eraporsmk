<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Traits\Uuid;
class Prestasi extends Model
{
    use Uuid;
    public $incrementing = false;
	protected $table = 'prestasi';
	protected $primaryKey = 'prestasi_id';
	protected $guarded = [];
	public function siswa(){
		return $this->hasOneThrough(
            'App\Anggota_rombel',
            'App\Siswa',
            'siswa_id',
            'siswa_id',
            'anggota_rombel_id',
            'siswa_id'
        );
	}
}
