<?php

namespace Enersys\Listeners;

use Auth;
use GeoIP;
use Request;
use Storage;
use Carbon\Carbon;
use Illuminate\Auth\Events\Login;

/**
 * Class AuthLoginNotification
 *
 * Listen for user to login and update the last connection attribute
 *
 * @package Enersys\Listeners
 * @version $Id$
 */
class AuthLoginNotification
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
    }

    /**
     * Handle the event.
     *
     * Save The Connection Time And Save the Request Ip 
     *
     * @param  AuthLoginEventHandler $event The event.
     * @return void
     */
    public function handle(Login $event)
    {        
        $user = $event->user;
        $user->last_connection = Carbon::now();
        $location = geoip($ip = $_SERVER['REMOTE_ADDR']);
        $user->last_connection_location = $location->city;
        $user->save();

        if ($user->role != 2){
            $message = $user->firstname . ' , ' . $user->lastname . ' ; ' . $user->company->name . ' ; ' . $user->last_connection . ' ; ' . $user->last_connection_location . ' \ ';
            Storage::disk('log_disk')->append('login_activity.txt', $message);
        }
        else {
            $message = $user->firstname . ' , ' . $user->lastname . ' ; ' . ' None ' . ' ; ' . $user->last_connection . ' ; ' . $user->last_connection_location . ' \ ';
            Storage::disk('log_disk')->append('login_activity.txt', $message);
        }
    }
}
