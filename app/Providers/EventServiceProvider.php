<?php

namespace App\Providers;

use Illuminate\Support\Facades\Event;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        'App\Events\NewLike' => [
            'App\Listeners\LikeNotification',
        ],
        'App\Events\NewComment' => [
            'App\Listeners\CommentNotification',
        ],
        'App\Events\NewFollow' => [
            'App\Listeners\FollowNotification',
        ],
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
