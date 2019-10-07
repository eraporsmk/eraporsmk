<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
//use App\Traits\Uuid;
use Ramsey\Uuid\Exception\UnsatisfiedDependencyException;
use Ramsey\Uuid\Uuid;
use Illuminate\Database\Eloquent\SoftDeletes;
class Kd_nilai extends Model
{
    //use Uuid;
	use SoftDeletes;
    public $incrementing = false;
	protected $table = 'kd_nilai';
	protected $primaryKey = 'kd_nilai_id';
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
			foreach ($model->nilai()->get() as $model) {
				$model->delete();
			}
		});
    }
	public function kompetensi_dasar(){
		return $this->hasOne('App\Kompetensi_dasar', 'kompetensi_dasar_id', 'kompetensi_dasar_id');
	}
	public function rencana_penilaian(){
		return $this->hasOne('App\Rencana_penilaian', 'rencana_penilaian_id', 'rencana_penilaian_id');
	}
	public function nilai(){
        return $this->hasMany('App\Nilai', 'kd_nilai_id', 'kd_nilai_id');
    }
	public function nilai_kd_pengetahuan(){
        return $this->hasMany('App\NilaiPengetahuanPerKd', 'kompetensi_dasar_id', 'kompetensi_dasar_id');
    }
	public function nilai_kd_keterampilan(){
        return $this->hasMany('App\NilaiKeterampilanPerKd', 'kompetensi_dasar_id', 'kompetensi_dasar_id');
    }
}
