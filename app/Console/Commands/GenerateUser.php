<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use App\Siswa;
use App\User;
use App\Role;
class GenerateUser extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'generate:user {query} {user} {sekolah}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $arguments = $this->arguments();
        $query = $arguments['query'];
        $user = $arguments['user'];
        $sekolah = $arguments['sekolah'];
        if($query == 'pd'){
            Siswa::whereNotIn('peserta_didik_id', function($query) use ($user) {
				$query->select('peserta_didik_id')->from('users')->whereNotNull('peserta_didik_id')->where('sekolah_id', '=', $user->sekolah_id);
			})->where('sekolah_id', '=', $user->sekolah_id)->chunk(50, function ($find_siswa) use ($user, $sekolah){
				foreach($find_siswa as $siswa){
					$random = Str::random(8);
					$find_user = User::where('email', $siswa->email)->first();
					$siswa->email = ($siswa->email != $user->email) ? $siswa->email : strtolower($random).'@erapor-smk.net';
					$siswa->email = ($siswa->email != $sekolah->email) ? $siswa->email : strtolower($random).'@erapor-smk.net';
					$siswa->email = (!$find_user) ? $siswa->email : strtolower($random).'@erapor-smk.net';
					$siswa->email = strtolower($siswa->email);
					$find_user_email = User::where('email', $siswa->email)->first();
					if($find_user_email){
						$siswa->email = strtolower($random).'@erapor-smk.net';
					}
					$new_password = strtolower(Str::random(8));
					$insert_user = array(
						'name' => $siswa->nama,
						'email' => $siswa->email,
						'nisn'	=> $siswa->nisn,
						'password' => Hash::make($new_password),
						'last_sync'	=> date('Y-m-d H:i:s'),
						'sekolah_id'	=> $user->sekolah_id,
						'password_dapo'	=> md5($new_password),
						'peserta_didik_id'	=> $siswa->peserta_didik_id,
						'default_password' => $new_password,
					);
					$create_user = User::updateOrCreate(
						['peserta_didik_id' => $siswa->peserta_didik_id],
						$insert_user
					);
					$adminRole = Role::where('name', 'siswa')->first();
					$CheckadminRole = DB::table('role_user')->where('user_id', $create_user->user_id)->first();
					if(!$CheckadminRole){
						$create_user->attachRole($adminRole);
					}
				}
			});
			User::where('sekolah_id', $sekolah->sekolah_id)->whereRoleIs('siswa')->chunk(50, function ($all_pengguna) {
				foreach($all_pengguna as $pengguna){
					if(Hash::check(12345678, $pengguna->password) || !$pengguna->default_password){
						$new_password = strtolower(Str::random(8));
						$pengguna->password = Hash::make($new_password);
						$pengguna->default_password = $new_password;
						$pengguna->save();
					}
				}
			});
        }
    }
}
