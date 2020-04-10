<?php

namespace App\Providers;

use Illuminate\Support\Facades\Event;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        Registered::class => [
            SendEmailVerificationNotification::class,
        ],
		'Illuminate\Auth\Events\Login' => [
            'App\Listeners\LogSuccessfulLogin',
        ],
		'Illuminate\Auth\Events\Logout' => [
        	'App\Listeners\LogSuccessfulLogout',
    	],
		/*\Codedge\Updater\Events\UpdateAvailable::class => [
			\Codedge\Updater\Listeners\SendUpdateAvailableNotification::class
		], // [3]
		\Codedge\Updater\Events\UpdateSucceeded::class => [
			\Codedge\Updater\Listeners\SendUpdateSucceededNotification::class
		], // [3]*/
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        parent::boot();
        //
    }
}
