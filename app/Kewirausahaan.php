<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Traits\Uuid;
class Kewirausahaan extends Model
{
    use Uuid;
    public $incrementing = false;
	protected $table = 'kewirausahaan';
	protected $primaryKey = 'kewirausahaan_id';
	protected $guarded = [];
	public function anggota_rombel(){
		return $this->hasOne('App\Anggota_rombel', 'anggota_rombel_id', 'anggota_rombel_id');
    }
    public function anggota(){
        return $this->hasMany('App\Anggota_kewirausahaan', 'kewirausahaan_id', 'kewirausahaan_id');
    }
    public function nama_anggota()
    {
        $nama_siswa = strtoupper($this->anggota_rombel->siswa->nama);
        return $nama_siswa.'<br>'.implode('<br>', $this->anggota->map(function ($anggota) {
            return strtoupper($anggota->anggota_rombel->siswa->nama);
        })->toArray());
    }
}
