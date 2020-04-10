<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Traits\Uuid;
class Guru extends Model
{
	use Uuid;
    public $incrementing = false;
	protected $table = 'guru';
	protected $primaryKey = 'guru_id';
	protected $guarded = [];
	/*protected $fillable = [
        'guru_id', 'guru_id_dapodik', 'sekolah_id', 'nama', 'nuptk', 'nip', 'jenis_kelamin', 'tempat_lahir', 'tanggal_lahir', 'nik', 'jenis_ptk_id', 'agama_id', 'alamat', 'rt', 'rw', 'desa_kelurahan', 'kecamatan', 'kode_pos', 'no_hp', 'email', 'photo', 'last_sync'
    ];*/
	public function agama(){
		return $this->hasOne('App\Agama', 'id', 'agama_id');
	}
	public function jenis_ptk(){
		return $this->hasOne('App\Jenis_ptk', 'jenis_ptk_id', 'jenis_ptk_id');
	}
	public function bimbing_pd(){
		return $this->hasOne('App\Bimbing_pd', 'guru_id', 'guru_id');
	}
	public function status_kepegawaian(){
		return $this->hasOne('App\Status_kepegawaian', 'status_kepegawaian_id', 'status_kepegawaian_id');
	}
	//public function dudi(){
		//return $this->hasOne('App\Dudi', 'dudi_id', 'dudi_id');
	//}
	public function gelar_depan(){
		return $this->hasManyThrough(
            'App\Gelar',
            'App\Gelar_ptk',
            'guru_id', // Foreign key on users table...
            'gelar_akademik_id', // Foreign key on history table...
            'guru_id', // Local key on suppliers table...
            'gelar_akademik_id' // Local key on users table...
        )->where('posisi_gelar', 1)->orderBy('kode', 'desc');
	}
	public function gelar_belakang(){
		return $this->hasManyThrough(
            'App\Gelar',
            'App\Gelar_ptk',
            'guru_id', // Foreign key on users table...
            'gelar_akademik_id', // Foreign key on history table...
            'guru_id', // Local key on suppliers table...
            'gelar_akademik_id' // Local key on users table...
        )->where('posisi_gelar', 2)->where('gelar_ptk.gelar_akademik_id', '<>', 99999)->orderBy('kode', 'desc');
	}
	public function dudi(){
		return $this->hasOneThrough(
            'App\Dudi',
            'App\Asesor',
            'guru_id', // Foreign key on users table...
            'dudi_id', // Foreign key on history table...
            'guru_id', // Local key on suppliers table...
            'dudi_id' // Local key on users table...
        );
	}
	public function pengguna(){
		return $this->hasOne('App\User', 'guru_id', 'guru_id');
	}
}
