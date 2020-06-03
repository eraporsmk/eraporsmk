<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Providers\HelperServiceProvider;
use Ramsey\Uuid\Exception\UnsatisfiedDependencyException;
use Ramsey\Uuid\Uuid;
class Rombongan_belajar extends Model
{
	use SoftDeletes;
    public $incrementing = false;
	protected $table = 'rombongan_belajar';
	protected $primaryKey = 'rombongan_belajar_id';
	protected $guarded = [];
	/*protected $fillable = [
        'rombongan_belajar_id', 'sekolah_id', 'semester_id', 'jurusan_id', 'jurusan_sp_id', 'kurikulum_id', 'nama', 'guru_id', 'guru_id_dapodik', 'tingkat', 'jenis_rombel', 'rombel_id_dapodik', 'kunci_nilai', 'rombongan_belajar_id_erapor', 'last_sync'
    ];*/
	protected static function boot(){
		parent::boot();
		static::creating(function ($model) {
            try {
                //$model->uuid = Generator::uuid4()->toString();
				$model->{$model->getKeyName()} = Uuid::uuid4()->toString();
            } catch (UnsatisfiedDependencyException $e) {
                abort(500, $e->getMessage());
            }
        });
		static::deleting(function($model) {
			foreach ($model->anggota_rombel()->get() as $anggota) {
				$anggota->delete();
			}
			foreach ($model->pembelajaran()->get() as $pembelajaran) {
				$pembelajaran->delete();
			}
		});
	}
	public function anggota_rombel(){
		return $this->hasMany('App\Anggota_rombel', 'rombongan_belajar_id', 'rombongan_belajar_id');
    }
	public function pembelajaran(){
		return $this->hasMany('App\Pembelajaran', 'rombongan_belajar_id', 'rombongan_belajar_id')->orderBy('kelompok_id')->orderBy('no_urut');
    }
	public function one_pembelajaran(){
		return $this->hasOne('App\Pembelajaran', 'rombongan_belajar_id', 'rombongan_belajar_id');
    }
	public function jurusan(){
		return $this->hasOne('App\Jurusan', 'jurusan_id', 'jurusan_id');
	}
	public function kurikulum(){
		return $this->hasOne('App\Kurikulum', 'kurikulum_id', 'kurikulum_id');
	}
	public function wali(){
		return $this->hasOne('App\Guru', 'guru_id', 'guru_id');
	}
	public function ekstrakurikuler(){
		return $this->hasOne('App\Ekstrakurikuler', 'rombongan_belajar_id', 'rombongan_belajar_id');
	}
	public function semester(){
		return $this->hasOne('App\Semester', 'semester_id', 'semester_id');
	}
	public function siswa(){
		return $this->hasManyThrough(
            'App\Siswa',
            'App\Anggota_rombel',
            'rombongan_belajar_id', // Foreign key on history table...
            'peserta_didik_id', // Foreign key on users table...
            'rombongan_belajar_id', // Local key on suppliers table...
            'peserta_didik_id' // Local key on users table...
        );
	}
	public function sekolah(){
		return $this->hasOne('App\Sekolah', 'sekolah_id', 'sekolah_id');
    }
	public function nilai(){
		return $this->hasManyThrough(
            'App\Nilai_akhir',
            'App\Pembelajaran',
            'rombongan_belajar_id', // Foreign key on history table...
            'pembelajaran_id', // Foreign key on users table...
            'rombongan_belajar_id', // Local key on suppliers table...
            'pembelajaran_id' // Local key on users table...
        );
	}
}
