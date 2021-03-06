<?php

namespace App;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

use Carbon\Carbon;

class Reply extends Model
{
    use RecordsActivity, Favoritable;

    protected $fillable = ['thread_id', 'user_id', 'body'];

    protected $with = ['favorites', 'creator'];

    protected $appends = ['isFavorited'];

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

    /**
     * wasPublishedAgo - determines if the reply was before given moment
     * @param  Carbon $before
     * @return  Boolean
     */
    public function wasPublishedAgo($before = null)
    {
        return $this->created_at->gt($before ?: Carbon::now()->subMinute());
    }
}
