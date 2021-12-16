<?php

namespace App;
use App\Traits\Uuid;
use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laratrust\Traits\LaratrustUserTrait;

class User extends Authenticatable
{
    use LaratrustUserTrait;
    use Notifiable;
	use Uuid;
	public $incrementing = false;
	protected $primaryKey = 'user_id';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
     /*
    protected $fillable = [
        'sekolah_id', 'name', 'email', 'password', 'password_dapo', 'last_sync', 'nisn', 'nuptk', 'peserta_didik_id', 'guru_id',
    ];
	*/
	protected $guarded = [];
    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];
	public function siswa(){
		//$semester = HelperServiceProvider::get_ta();
		return $this->hasOne('App\Siswa', 'peserta_didik_id', 'peserta_didik_id');
    }
    public function anggota_rombel(){
		return $this->hasOneThrough(
            'App\Anggota_rombel',
            'App\Siswa',
            'peserta_didik_id', // Foreign key on Sekolah_sasaran table...
            'peserta_didik_id', // Foreign key on Sekolah table...
            'peserta_didik_id', // Local key on Rapor_mutu table...
            'peserta_didik_id' // Local key on Sekolah_sasaran table...
        );
    }
}
