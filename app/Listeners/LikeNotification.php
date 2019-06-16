<?php

namespace App\Listeners;

use App\Events\NewLike;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Auth;
use App\Notification;

class LikeNotification
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
     * @param  NewLike  $event
     * @return void
     */
    public function handle(NewLike $event)
    {
        $name = Auth::user()->name;
        $username = Auth::user()->username;

        $notification = new Notification;
        $notification->notification_html = "<a href='" . route('user.show', $username) . "'>" . $name . "</a> liked your tweet";
        $notification->tweet_id = $event->tweet->id;
        $notification->user_id = $event->tweet->user_id;
        $notification->type = 1;
        $notification->save();
    }
}
