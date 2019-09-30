<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
//use App\Traits\Uuid;
use Ramsey\Uuid\Exception\UnsatisfiedDependencyException;
use Ramsey\Uuid\Uuid;
use Illuminate\Database\Eloquent\SoftDeletes;
class Rencana_penilaian extends Model
{
    //use Uuid;
    use SoftDeletes;
    public $incrementing = false;
	protected $table = 'rencana_penilaian';
	protected $primaryKey = 'rencana_penilaian_id';
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
			foreach ($model->kd_nilai()->get() as $model) {
				$model->delete();
			}
		});
    }
	public function kd_nilai(){
        return $this->hasMany('App\Kd_nilai', 'rencana_penilaian_id', 'rencana_penilaian_id');
    }
	public function pembelajaran(){
		return $this->hasOne('App\Pembelajaran', 'pembelajaran_id', 'pembelajaran_id');
	}
	public function metode(){
		return $this->hasOne('App\Teknik_penilaian', 'teknik_penilaian_id', 'metode_id');
	}
	public function teknik_penilaian(){
		return $this->hasOne('App\Teknik_penilaian', 'teknik_penilaian_id', 'metode_id');
	}
	public function rombongan_belajar(){
		return $this->hasOneThrough(
            'App\Rombongan_belajar',
            'App\Pembelajaran',
            'pembelajaran_id', // Foreign key on users table...
            'rombongan_belajar_id', // Foreign key on history table...
            'pembelajaran_id', // Local key on suppliers table...
            'rombongan_belajar_id' // Local key on users table...
        );
	}
	public function nilai(){
		return $this->hasManyThrough(
            'App\Nilai',
            'App\Kd_nilai',
            'rencana_penilaian_id', // Foreign key on users table...
            'kd_nilai_id', // Foreign key on history table...
            'rencana_penilaian_id', // Local key on suppliers table...
            'kd_nilai_id' // Local key on users table...
        );
	}
}
