<?php

namespace App;

trait Favoritable
{

    protected static function bootFavoritable()
    {
        static::deleting(function ($model) {
            $model->favorites->each->delete();
        });
    }

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

    public function handleFavorite($user)
    {
        $attributes = ['user_id' => $user];

        if (!$this->isFavorited($user)) {
            $this->favorites()->create($attributes);

            return true;
        } else {
            // EVENTS DO NOT FIRE ON QUERY 
            // ENSURE THAT EVENTS FIRE ON MODEL
            $this->favorites()
                ->where(['user_id' => $user])
                ->get()
                ->each->delete();

            return false;
        }
    }

    public function isFavorited($user)
    {
        return $this->favorites()->where(['user_id' => $user])->exists();
    }

    public function getIsFavoritedAttribute($user)
    {
        return $this->isFavorited(auth()->id());
    }
}
