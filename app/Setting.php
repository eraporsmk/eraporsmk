<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
	public $timestamps = false;
    protected $table = 'settings';
    protected $primaryKey = 'key';
    protected $fillable = ['key', 'value'];
    public function get($query, $key)
    {
        $key = $query->where('key', '=', $key)->first();
        return $key->value;
    }
	static function scopeOfType($query, $key)
    {
        $key = $query->where('key', $key)->first();
		return $key->value;
    }
}
