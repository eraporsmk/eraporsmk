<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Ramsey\Uuid\Exception\UnsatisfiedDependencyException;
use Ramsey\Uuid\Uuid;
use App\Providers\HelperServiceProvider;
use Illuminate\Database\Eloquent\SoftDeletes;
class Siswa extends Model
{
    //use Uuid;
	use SoftDeletes;
    public $incrementing = false;
	protected $table = 'peserta_didik';
	protected $primaryKey = 'peserta_didik_id';
	protected $guarded = [];
	protected static function boot(){
		parent::boot();
		static::creating(function ($model) {
            try {
                $model->{$model->getKeyName()} = Uuid::uuid4()->toString();
            } catch (UnsatisfiedDependencyException $e) {
                abort(500, $e->getMessage());
            }
        });
		static::deleting(function($model) {
			foreach ($model->anggota_rombel()->get() as $model) {
				$model->delete();
			}
		});
    }
	public function all_anggota_rombel(){
		return $this->hasMany('App\Anggota_rombel', 'peserta_didik_id', 'peserta_didik_id');
    }
	public function agama(){
		$hasColumn = DB::select("SELECT true as exists FROM information_schema.columns 
		WHERE table_schema='ref' AND table_name='agama' AND column_name='agama_id';");
		if (isset($hasColumn) && $hasColumn && $hasColumn[0]->exists) {
			return $this->hasOne('App\Agama', 'agama_id', 'agama_id');
		} else {
			return $this->hasOne('App\Agama', 'id', 'agama_id');
		}
	}
	public function pekerjaan_ayah(){
		return $this->hasOne('App\Pekerjaan', 'pekerjaan_id', 'kerja_ayah');
	}
	public function pekerjaan_ibu(){
		return $this->hasOne('App\Pekerjaan', 'pekerjaan_id', 'kerja_ibu');
	}
	public function pekerjaan_wali(){
		return $this->hasOne('App\Pekerjaan', 'pekerjaan_id', 'kerja_wali');
	}
	public function anggota_rombel(){
		return $this->hasOne('App\Anggota_rombel', 'peserta_didik_id', 'peserta_didik_id');
    }
	public function sekolah(){
		return $this->hasOne('App\Sekolah', 'sekolah_id', 'sekolah_id');
    }
	public function get_kecamatan(){
		return $this->hasOne('App\Mst_wilayah', 'kode_wilayah', 'kode_wilayah');
    }
	public function kelas(){
		return $this->hasOneThrough(
            'App\Rombongan_belajar',
            'App\Anggota_rombel',
            'peserta_didik_id', // Foreign key on users table...
            'rombongan_belajar_id', // Foreign key on history table...
            'peserta_didik_id', // Local key on suppliers table...
            'rombongan_belajar_id' // Local key on users table...
        );
	}
	public function nilai_sikap(){
		return $this->hasManyThrough(
			'App\Nilai_sikap',
            'App\Anggota_rombel',
            'peserta_didik_id', // Foreign key on users table...
            'anggota_rombel_id', // Foreign key on history table...
            'peserta_didik_id', // Local key on suppliers table...
            'rombongan_belajar_id' // Local key on users table...
		);
	}
}
