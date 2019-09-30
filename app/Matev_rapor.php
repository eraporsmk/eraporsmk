<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Ramsey\Uuid\Exception\UnsatisfiedDependencyException;
use Ramsey\Uuid\Uuid as Generator;
class Matev_rapor extends Model
{
    protected $connection = 'dapodik';
	public $incrementing = false;
	protected $table = 'nilai.matev_rapor';
	protected $primaryKey = 'id_evaluasi';
	protected $guarded = [];
	public $timestamps = false;
	protected static function boot(){
		parent::boot();
		static::creating(function ($model) {
            try {
                $model->{$model->getKeyName()} = Generator::uuid4()->toString();
				$model->updater_id = Generator::uuid4()->toString();
            } catch (UnsatisfiedDependencyException $e) {
                abort(500, $e->getMessage());
            }
        });
    }
	public function rombongan_belajar(){
		return $this->hasOne('App\Rombongan_belajar_dapodik', 'rombongan_belajar_id', 'rombongan_belajar_id');
	}
}
