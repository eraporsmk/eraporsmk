<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Ramsey\Uuid\Exception\UnsatisfiedDependencyException;
use Ramsey\Uuid\Uuid as Generator;
class Nilai_rapor_dapodik extends Model
{
    protected $connection = 'dapodik';
	public $incrementing = false;
	protected $table = 'nilai.nilai_rapor';
	protected $primaryKey = 'nilai_id';
	protected $guarded = [];
	public $timestamps = false;
	protected static function boot(){
		parent::boot();
		static::creating(function ($model) {
            try {
                $model->{$model->getKeyName()} = Generator::uuid4()->toString();
            } catch (UnsatisfiedDependencyException $e) {
                abort(500, $e->getMessage());
            }
        });
    }
	public function matev_rapor(){
		return $this->hasOne('App\Matev_rapor', 'id_evaluasi', 'id_evaluasi');
	}
}
