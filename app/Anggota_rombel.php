<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Traits\Uuid;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Providers\HelperServiceProvider;
use Illuminate\Support\Facades\DB;
class Anggota_rombel extends Model
{
	use \Staudenmeir\EloquentHasManyDeep\HasRelationships;
	use \Staudenmeir\EloquentEagerLimit\HasEagerLimit;
    use Uuid;
	use SoftDeletes;
    public $incrementing = false;
	protected $table = 'anggota_rombel';
	protected $primaryKey = 'anggota_rombel_id';
	protected $guarded = [];
	/*protected static function boot(){
		parent::boot();
		static::creating(function ($model) {
            try {
                $model->{$model->getKeyName()} = Uuid::uuid4()->toString();
            } catch (UnsatisfiedDependencyException $e) {
                abort(500, $e->getMessage());
            }
        });
		static::deleting(function($model) {
			//foreach ($model->peserta_didik()->get() as $model) {
				//$model->delete();
			//}
		});
    }*/
	public function rombongan_belajar(){
		//$semester = HelperServiceProvider::get_ta();
		return $this->hasOne('App\Rombongan_belajar', 'rombongan_belajar_id', 'rombongan_belajar_id');
    }
	public function scopeOrder($query){
        //return $query->where('done', $flag);
		return $query->select('anggota_rombel.*', DB::raw('(SELECT nama FROM peserta_didik WHERE anggota_rombel.peserta_didik_id = peserta_didik.peserta_didik_id ) as sort'))->orderBy('sort', 'asc');
    }
	public function siswa(){
		return $this->hasOne('App\Siswa', 'peserta_didik_id', 'peserta_didik_id');
	}
	public function nilai_akhir_pengetahuan(){
		return $this->hasOne('App\Nilai_akhir', 'anggota_rombel_id', 'anggota_rombel_id')->where('kompetensi_id', '=', 1);
	}
	public function nilai_akhir_keterampilan(){
		return $this->hasOne('App\Nilai_akhir', 'anggota_rombel_id', 'anggota_rombel_id')->where('kompetensi_id', '=', 2);
	}
	public function nilai_akhir_pk(){
		return $this->hasOne('App\Nilai_akhir', 'anggota_rombel_id', 'anggota_rombel_id')->where('kompetensi_id', '=', 3);
	}
	public function nilai_kd_pengetahuan(){
		return $this->hasMany('App\NilaiPengetahuanPerKd', 'anggota_rombel_id', 'anggota_rombel_id')->where('kompetensi_id', '=', 1);
	}
	public function nilai_kd_keterampilan(){
		return $this->hasMany('App\NilaiKeterampilanPerKd', 'anggota_rombel_id', 'anggota_rombel_id')->where('kompetensi_id', '=', 2);
	}
	public function nilai_kd_pk(){
		return $this->hasMany('App\NilaiPkPerKd', 'anggota_rombel_id', 'anggota_rombel_id')->where('kompetensi_id', '=', 3)->with('kd_nilai.kompetensi_dasar');
	}
	public function nilai_remedial(){
		return $this->hasOne('App\Remedial', 'anggota_rombel_id', 'anggota_rombel_id');
	}
	public function nilai_ekskul(){
		return $this->hasOne('App\Nilai_ekstrakurikuler', 'anggota_rombel_id', 'anggota_rombel_id');
	}
	public function catatan_wali(){
		return $this->hasOne('App\Catatan_wali', 'anggota_rombel_id', 'anggota_rombel_id');
	}
	public function nilai_rapor(){
		//return $this->hasMany('App\Nilai_rapor', 'anggota_rombel_id', 'anggota_rombel_id')->orderBy('total_nilai', 'ASC');
		return $this->hasMany('App\Nilai_rapor', 'anggota_rombel_id', 'anggota_rombel_id')->orderByRaw('((nilai_p * rasio_p) + (nilai_k * rasio_k))');
	}
	public function nilai_rapor_pk(){
		return $this->hasOne('App\Nilai_rapor', 'anggota_rombel_id', 'anggota_rombel_id');
	}
	public function deskripsi_mata_pelajaran(){
		return $this->hasMany('App\Deskripsi_mata_pelajaran', 'anggota_rombel_id', 'anggota_rombel_id');
	}
	public function v_nilai_akhir_p(){
		return $this->hasOne('App\NilaiAkhirPengetahuan', 'anggota_rombel_id', 'anggota_rombel_id');
	}
	public function v_nilai_akhir_k(){
		return $this->hasOne('App\NilaiAkhirKeterampilan', 'anggota_rombel_id', 'anggota_rombel_id');
	}
	public function v_nilai_akhir_pk(){
		return $this->hasOne('App\NilaiAkhirPk', 'anggota_rombel_id', 'anggota_rombel_id');
	}
	public function nilai_sikap(){
		return $this->hasMany('App\Nilai_sikap', 'anggota_rombel_id', 'anggota_rombel_id');
	}
	public function kehadiran(){
		return $this->hasOne('App\Absensi', 'anggota_rombel_id', 'anggota_rombel_id');
	}
	public function all_nilai_ekskul(){
		return $this->hasManyThrough(
            'App\Nilai_ekstrakurikuler',
            'App\Anggota_rombel',
            'peserta_didik_id', // Foreign key on history table...
            'anggota_rombel_id', // Foreign key on users table...
            'peserta_didik_id', // Local key on suppliers table...
            'anggota_rombel_id' // Local key on users table...
        );
	}
	public function anggota_ekskul(){
		return $this->hasManyThrough(
            'App\Anggota_rombel',
            'App\Siswa',
            'peserta_didik_id', // Foreign key on history table...
            'peserta_didik_id', // Foreign key on users table...
            'peserta_didik_id', // Local key on suppliers table...
            'peserta_didik_id' // Local key on users table...
        );
	}
	public function kelas_ekskul(){
		return $this->hasOne('App\Rombongan_belajar', 'rombongan_belajar_id', 'rombongan_belajar_id');
		/*return $this->hasOneThrough(
            'App\Ekstrakurikuler',
            'App\Rombongan_belajar',
            'rombongan_belajar_id', // Foreign key on users table...
            'rombongan_belajar_id', // Foreign key on history table...
            'rombongan_belajar_id', // Local key on suppliers table...
            'rombongan_belajar_id' // Local key on users table...
        );*/
	}
	public function prakerin(){
		return $this->hasOne('App\Prakerin', 'anggota_rombel_id', 'anggota_rombel_id');
	}
	public function all_prakerin(){
		return $this->hasMany('App\Prakerin', 'anggota_rombel_id', 'anggota_rombel_id');
	}
	public function prestasi(){
		return $this->hasMany('App\Prestasi', 'anggota_rombel_id', 'anggota_rombel_id');
	}
	public function kenaikan(){
		return $this->hasOne('App\Kenaikan_kelas', 'anggota_rombel_id', 'anggota_rombel_id');
	}
	public function nilai_ukk(){
		return $this->hasOne('App\Nilai_ukk', 'anggota_rombel_id', 'anggota_rombel_id');
	}
	public function sekolah(){
		return $this->hasOne('App\Sekolah', 'sekolah_id', 'sekolah_id');
    }
	public function catatan_ppk(){
		return $this->hasOne('App\Catatan_ppk', 'anggota_rombel_id', 'anggota_rombel_id');
	}
	public function nilai_akhir_legger(){
		return $this->hasOneDeep(
			'App\Nilai_akhir', 
			['App\Rombongan_belajar', 'App\Pembelajaran'],
			[
				'rombongan_belajar_id', // Foreign key on the "users" table.
				'rombongan_belajar_id',    // Foreign key on the "posts" table.
				'pembelajaran_id'     // Foreign key on the "comments" table.
			],
			[
				'rombongan_belajar_id', // Local key on the "countries" table.
				'rombongan_belajar_id', // Local key on the "users" table.
				'pembelajaran_id'  // Local key on the "posts" table.
			]
		);
	}
	public function nilai_rapor_legger(){
		return $this->hasOneDeep(
			'App\Nilai_rapor', 
			['App\Rombongan_belajar', 'App\Pembelajaran'],
			[
				'rombongan_belajar_id', // Foreign key on the "users" table.
				'rombongan_belajar_id',    // Foreign key on the "posts" table.
				'pembelajaran_id'     // Foreign key on the "comments" table.
			],
			[
				'rombongan_belajar_id', // Local key on the "countries" table.
				'rombongan_belajar_id', // Local key on the "users" table.
				'pembelajaran_id'  // Local key on the "posts" table.
			]
		);
	}
	public function nilai_us(){
		return $this->hasOne('App\Nilai_us', 'anggota_rombel_id', 'anggota_rombel_id');
	}
	public function nilai_un(){
		return $this->hasOne('App\Nilai_un', 'anggota_rombel_id', 'anggota_rombel_id');
	}
	public function nilai_budaya_kerja(){
		return $this->hasMany(Nilai_budaya_kerja::class, 'anggota_rombel_id', 'anggota_rombel_id');
	}
	public function catatan_budaya_kerja(){
		return $this->hasOne(Catatan_budaya_kerja::class, 'anggota_rombel_id', 'anggota_rombel_id');
	}
}
