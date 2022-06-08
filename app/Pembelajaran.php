<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Traits\Uuid;
use App\Providers\HelperServiceProvider;
use Illuminate\Database\Eloquent\SoftDeletes;
class Pembelajaran extends Model
{
	use Uuid;
	use SoftDeletes;
    public $incrementing = false;
	protected $table = 'pembelajaran';
	protected $primaryKey = 'pembelajaran_id';
	protected $guarded = [];
	public function guru(){
		return $this->hasOne('App\Guru', 'guru_id', 'guru_id');
	}
	public function pengajar(){
		return $this->hasOne('App\Guru', 'guru_id', 'guru_pengajar_id');
	}
	public function mata_pelajaran(){
		return $this->hasOne('App\Mata_pelajaran', 'mata_pelajaran_id', 'mata_pelajaran_id');
	}
	public function rombongan_belajar(){
		return $this->hasOne('App\Rombongan_belajar', 'rombongan_belajar_id', 'rombongan_belajar_id');
	}
	public function rencana_pengetahuan(){
		return $this->hasMany('App\Rencana_penilaian', 'pembelajaran_id', 'pembelajaran_id')->where('kompetensi_id', '=', 1);
	}
	public function rencana_keterampilan(){
		return $this->hasMany('App\Rencana_penilaian', 'pembelajaran_id', 'pembelajaran_id')->where('kompetensi_id', '=', 2);
	}
	public function rencana_pk(){
		return $this->hasMany('App\Rencana_penilaian', 'pembelajaran_id', 'pembelajaran_id')->where('kompetensi_id', '=', 3);
	}
	public function rencana_pengetahuan_dinilai(){
		return $this->hasMany('App\Rencana_penilaian', 'pembelajaran_id', 'pembelajaran_id')->where('kompetensi_id', '=', 1)->whereHas('nilai');
	}
	public function rencana_keterampilan_dinilai(){
		return $this->hasMany('App\Rencana_penilaian', 'pembelajaran_id', 'pembelajaran_id')->where('kompetensi_id', '=', 2)->whereHas('nilai');
	}
	public function rencana_pk_dinilai(){
		return $this->hasMany('App\Rencana_penilaian', 'pembelajaran_id', 'pembelajaran_id')->where('kompetensi_id', '=', 3)->whereHas('nilai');
	}
	public function nilai_akhir_pengetahuan(){
		return $this->hasOne('App\Nilai_akhir', 'pembelajaran_id', 'pembelajaran_id')->where('kompetensi_id', '=', 1);
	}
	public function nilai_akhir_keterampilan(){
		return $this->hasOne('App\Nilai_akhir', 'pembelajaran_id', 'pembelajaran_id')->where('kompetensi_id', '=', 2);
	}
	public function nilai_akhir_pk(){
		return $this->hasOne('App\Nilai_akhir', 'pembelajaran_id', 'pembelajaran_id')->where('kompetensi_id', '=', 3);
	}
	public function anggota_rombel(){
		return $this->hasManyThrough(
            'App\Anggota_rombel',
			'App\Rombongan_belajar',
			'rombongan_belajar_id',
			'rombongan_belajar_id',
			'rombongan_belajar_id',
			'rombongan_belajar_id'
        );
    }
	public function one_anggota_rombel(){
		return $this->hasOneThrough(
            'App\Anggota_rombel',
			'App\Rombongan_belajar',
			'rombongan_belajar_id',
			'rombongan_belajar_id',
			'rombongan_belajar_id',
			'rombongan_belajar_id'
        );
    }
	public function rencana_penilaian(){
		return $this->hasMany('App\Rencana_penilaian', 'pembelajaran_id', 'pembelajaran_id');
	}
	public function kd_nilai()
    {
        return $this->hasManyThrough(
            'App\Kd_nilai',
            'App\Rencana_penilaian',
            'pembelajaran_id', // Foreign key on users table...
            'rencana_penilaian_id', // Foreign key on posts table...
            'pembelajaran_id', // Local key on countries table...
            'rencana_penilaian_id' // Local key on users table...
        );
    }
	public function kd_nilai_capaian()
    {
        return $this->hasOneThrough(
            'App\Kd_nilai',
            'App\Rencana_penilaian',
            'pembelajaran_id', // Foreign key on users table...
            'rencana_penilaian_id', // Foreign key on posts table...
            'pembelajaran_id', // Local key on countries table...
            'rencana_penilaian_id' // Local key on users table...
        );
    }
	public function kd_nilai_p()
    {
        return $this->hasManyThrough(
            'App\Kd_nilai',
            'App\Rencana_penilaian',
            'pembelajaran_id', // Foreign key on users table...
            'rencana_penilaian_id', // Foreign key on posts table...
            'pembelajaran_id', // Local key on countries table...
            'rencana_penilaian_id' // Local key on users table...
        )->where('kompetensi_id', 1);
    }
	public function kd_nilai_k()
    {
        return $this->hasManyThrough(
            'App\Kd_nilai',
            'App\Rencana_penilaian',
            'pembelajaran_id', // Foreign key on users table...
            'rencana_penilaian_id', // Foreign key on posts table...
            'pembelajaran_id', // Local key on countries table...
            'rencana_penilaian_id' // Local key on users table...
        )->where('kompetensi_id', 2);
    }
	public function kd_nilai_pk()
    {
        return $this->hasManyThrough(
            'App\Kd_nilai',
            'App\Rencana_penilaian',
            'pembelajaran_id', // Foreign key on users table...
            'rencana_penilaian_id', // Foreign key on posts table...
            'pembelajaran_id', // Local key on countries table...
            'rencana_penilaian_id' // Local key on users table...
        )->where('kompetensi_id', 3);
    }
	public function kelompok(){
		return $this->hasOne('App\Kelompok', 'kelompok_id', 'kelompok_id');
	}
	public function rapor_pts(){
		//return $this->hasOne('App\Rapor_pts', 'pembelajaran_id', 'pembelajaran_id');
		return $this->hasMany('App\Rapor_pts', 'pembelajaran_id', 'pembelajaran_id');
	}
	public function nilai_akhir_p(){
		return $this->hasOneThrough(
            'App\Nilai_akhir',
            'App\Pembelajaran',
            'pembelajaran_id', // Foreign key on users table...
            'pembelajaran_id', // Foreign key on history table...
            'pembelajaran_id', // Local key on suppliers table...
            'pembelajaran_id' // Local key on users table...
        )->where('kompetensi_id', 1);
	}
	public function nilai_akhir_k(){
		return $this->hasOneThrough(
            'App\Nilai_akhir',
            'App\Pembelajaran',
            'pembelajaran_id', // Foreign key on users table...
            'pembelajaran_id', // Foreign key on history table...
            'pembelajaran_id', // Local key on suppliers table...
            'pembelajaran_id' // Local key on users table...
        )->where('kompetensi_id', 2);
	}
	public function nilai_akhir_is_pk(){
		return $this->hasOneThrough(
            'App\Nilai_akhir',
            'App\Pembelajaran',
            'pembelajaran_id', // Foreign key on users table...
            'pembelajaran_id', // Foreign key on history table...
            'pembelajaran_id', // Local key on suppliers table...
            'pembelajaran_id' // Local key on users table...
        )->where('kompetensi_id', 3);
	}
	public function nilai_rapor(){
		return $this->hasOne('App\Nilai_rapor', 'pembelajaran_id', 'pembelajaran_id');
	}
	public function deskripsi_mata_pelajaran(){
		return $this->hasMany('App\Deskripsi_mata_pelajaran', 'pembelajaran_id', 'pembelajaran_id');
	}
}
