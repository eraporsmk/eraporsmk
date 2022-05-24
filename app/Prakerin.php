<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Traits\Uuid;
class Prakerin extends Model
{
    use Uuid;
    public $incrementing = false;
	protected $table = 'prakerin';
	protected $primaryKey = 'prakerin_id';
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
    public function anggota_rombel(){
        return $this->hasOne(Anggota_rombel::class, 'anggota_rombel_id', 'anggota_rombel_id');
    }
}
