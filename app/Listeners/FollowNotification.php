<?php

namespace App\Listeners;

use App\Events\NewFollow;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Notification;
use Illuminate\Support\Facades\Auth;

class FollowNotification
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  NewFollow  $event
     * @return void
     */
    public function handle(NewFollow $event)
    {
        $name = Auth::user()->name;
        $username = Auth::user()->username;

        $notification = new Notification;
        $notification->notification_html = "<a href='" . route('user.show', $username) . "'>" . $name . "</a> followed you.";
        $notification->user_id = $event->user->id;
        $notification->type = 3;
        $notification->save();
    }
}
