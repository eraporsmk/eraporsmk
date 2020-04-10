<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Traits\Uuid;
use Illuminate\Database\Eloquent\SoftDeletes;
class Ekstrakurikuler extends Model
{
    use Uuid;
	use SoftDeletes;
    public $incrementing = false;
	protected $table = 'ekstrakurikuler';
	protected $primaryKey = 'ekstrakurikuler_id';
	protected $guarded = [];
	public function rombongan_belajar(){
		return $this->hasOne('App\Rombongan_belajar', 'rombongan_belajar_id', 'rombongan_belajar_id');
	}
	public function guru(){
		return $this->hasOne('App\Guru', 'guru_id', 'guru_id');
	}
	public function anggota_rombel(){
		return $this->hasMany('App\Anggota_rombel', 'rombongan_belajar_id', 'rombongan_belajar_id');
	}
	public function anggota_rombel_satuan(){
		return $this->hasOne('App\Anggota_rombel', 'rombongan_belajar_id', 'rombongan_belajar_id');
	}
	public function nilai_ekskul(){
		return $this->belongsTo('App\Nilai_ekstrakurikuler', 'ekstrakurikuler_id', 'ekstrakurikuler_id');
	}
	public function nilai_ekskul1(){
		//return $this->hasOne('App\Nilai_ekstrakurikuler', 'ekstrakurikuler_id', 'ekstrakurikuler_id');
		return $this->hasOneThrough(
			'App\Nilai_ekstrakurikuler',
            'App\Anggota_rombel',
            'anggota_rombel_id', // Foreign key on users table...
            'anggota_rombel_id', // Foreign key on history table...
            'ekstrakurikuler_id', // Local key on suppliers table...
            'anggota_rombel_id' // Local key on users table...
		);
	}
}
