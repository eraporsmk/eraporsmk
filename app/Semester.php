<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Semester extends Model
{
    public $incrementing = false;
	protected $table = 'ref.semester';
	protected $primaryKey = 'semester_id';
	protected $guarded = [];
}
