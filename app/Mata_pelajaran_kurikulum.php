<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Mata_pelajaran_kurikulum extends Model
{
	use \LaravelTreats\Model\Traits\HasCompositePrimaryKey;
    public $incrementing = false;
	protected $table = 'ref.mata_pelajaran_kurikulum';
	protected $primaryKey = ['kurikulum_id', 'mata_pelajaran_id', 'tingkat_pendidikan_id'];
	protected $guarded = [];
	public function mata_pelajaran(){
		return $this->hasOne('App\Mata_pelajaran', 'mata_pelajaran_id', 'mata_pelajaran_id');
    }
	public function kurikulum(){
		return $this->hasOne('App\Kurikulum', 'kurikulum_id', 'kurikulum_id');
    }
}
