<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Reply extends Model
{
    protected $fillable = ['thread_id', 'user_id', 'body'];

    protected $with = ['favorites', 'user'];


    public function thread()
    {
        return $this->belongsTo(Thread::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function favorites()
    {
        return $this->morphMany(Favorite::class, 'favorited');
    }

    public function favorite($user)
    {
        $attributes = ['user_id' => $user];

        if (!$this->isFavorited($user)) {
            return $this->favorites()->create($attributes);
        }
    }

    public function isFavorited($user)
    {
        // return $this->favorites()->where(['user_id' => $user])->exists();
        return !!$this->favorites->where(['user_id' => $user])->count();
    }
}
