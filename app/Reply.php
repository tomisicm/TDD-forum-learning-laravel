<?php

namespace App;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class Reply extends Model
{
    use RecordsActivity;

    protected $fillable = ['thread_id', 'user_id', 'body'];

    protected $with = ['favorites', 'creator'];

    // TODO: counting
    // protected static function boot()
    // {
    //     parent::boot();


    //     static::addGlobalScope('favoriteCount', function (Builder $builder) {
    //         $builder->withCount('favorites');
    //     });
    // }

    public function thread()
    {
        return $this->belongsTo(Thread::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'user_id');
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
}
