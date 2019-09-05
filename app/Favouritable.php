<?php

namespace App;

trait Favouritable
{
    public function favorites()
    {
        return $this->morphMany(Favorite::class, 'favorited');
    }

    // TODO: counting
    // /**
    //  * Get the number of favorites for the reply.
    //  *
    //  * @return integer
    //  */
    // public function getFavoritesCountAttribute()
    // {
    //     return $this->favorites->count();
    // }

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
