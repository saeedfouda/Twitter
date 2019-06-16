<?php

namespace App\Listeners;

use App\Events\NewComment;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Auth;
use App\Notification;

class CommentNotification
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
     * @param  NewComment  $event
     * @return void
     */
    public function handle(NewComment $event)
    {
        $name = Auth::user()->name;
        $username = Auth::user()->username;

        $notification = new Notification;
        $notification->notification_html = "<a href='" . route('user.show', $username) . "'>" . $name . "</a> commented on your tweet";
        $notification->tweet_id = $event->comment->tweet->id;
        $notification->user_id = $event->comment->tweet->user_id;
        $notification->type = 2;
        $notification->extra_id = $event->comment->id;
        $notification->save();
    }
}
