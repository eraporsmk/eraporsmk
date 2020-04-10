<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Traits\Uuid;
class Nilai_ekstrakurikuler extends Model
{
    use Uuid;
    public $incrementing = false;
	protected $table = 'nilai_ekstrakurikuler';
	protected $primaryKey = 'nilai_ekstrakurikuler_id';
	protected $guarded = [];
	public function ekstrakurikuler(){
		return $this->hasOne('App\Ekstrakurikuler', 'ekstrakurikuler_id', 'ekstrakurikuler_id');
	}
	public function rombongan_belajar(){
		return $this->hasOneThrough(
            'App\Rombongan_belajar',
            'App\Anggota_rombel',
            'anggota_rombel_id', // Foreign key on history table...
            'rombongan_belajar_id', // Foreign key on users table...
            'anggota_rombel_id', // Local key on suppliers table...
            'rombongan_belajar_id' // Local key on users table...
        );
	}
}
