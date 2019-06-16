<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Tweet extends Model
{
    public function user()
    {
        return $this->belongsTo(\App\User::class);
    }

    public function likes()
    {
        return $this->belongsToMany(\App\User::class, 'likes', 'tweet_id', 'user_id');
    }

    public function comments()
    {
        return $this->hasMany(\App\Comment::class);
    }

}
