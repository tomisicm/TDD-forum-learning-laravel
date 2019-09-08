<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
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
        'name', 'email', 'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /**
     * getRouteKeyName - Route key name
     * @return string
     */
    public function getRouteKeyName()
    {
        return 'name';
    }

    /**
     * threads - relation with \App\Thread
     * @return \Illuminate\Database\Eloquent\Relations\hasMany
     */
    public function threads()
    {
        return $this->hasMany(Thread::class)->latest();
    }

    /**
     * activity - relation with \App\Activity
     * @return \Illuminate\Database\Eloquent\Relations\hasMany
     */
    public function activity()
    {
        return $this->hasMany(Activity::class);
    }

    /**
     * latestReply - relation with \App\Reply
     * @return \Illuminate\Database\Eloquent\Relations\hasOne
     */
    public function latestReply()
    {
        return $this->hasOne(Reply::class)->latest();
    }
}
