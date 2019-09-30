<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Traits\Uuid;
use Illuminate\Database\Eloquent\SoftDeletes;
class Nilai_akhir extends Model
{
    use Uuid;
    use SoftDeletes;
    public $incrementing = false;
	protected $table = 'nilai_akhir';
	protected $primaryKey = 'nilai_akhir_id';
	protected $guarded = [];
}
