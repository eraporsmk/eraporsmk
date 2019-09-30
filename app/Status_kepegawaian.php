<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Status_kepegawaian extends Model
{
    protected $table = 'ref.status_kepegawaian';
	protected $primaryKey = 'status_kepegawaian_id';
	protected $guarded = [];
}
