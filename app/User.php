<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'username', 'photo', 'email', 'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    public function tweets()
    {
        return $this->hasMany(\App\Tweet::class);
    }

    public function following()
    {
        return $this->belongsToMany(\App\User::class, 'followers', 'follower_id', 'leader_id');
    }

    public function followers()
    {
        return $this->belongsToMany(\App\User::class, 'followers', 'leader_id', 'follower_id');
    }

    public function comments()
    {
        return $this->hasMany(\App\Comment::class);
    }

    public function messages()
    {
        return $this->hasMany(\App\Message::class);
    }

    public function notifications()
    {
        return $this->hasMany(\App\Notification::class);
    }
}
