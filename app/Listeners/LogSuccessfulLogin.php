<?php

namespace App\Listeners;

use Illuminate\Auth\Events\Login;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Http\Request;
class LogSuccessfulLogin
{
    /**
     * Create the event listener.
     *
	 * @param  Request  $request
     * @return void
     */
    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    /**
     * Handle the event.
     *
     * @param  Login  $event
     * @return void
     */
    public function handle(Login $event)
    {
		$user = $event->user;
		session(['semester_id' => $this->request->semester_id]);
		session(['sekolah_id' => $user->sekolah_id]);
		$user->periode_aktif = $this->request->semester_id;
        $user->last_login_at = date('Y-m-d H:i:s');
        $user->last_login_ip = $this->request->ip();
        $user->save();
    }
}
