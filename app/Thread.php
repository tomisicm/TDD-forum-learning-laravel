<?php

namespace App;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class Thread extends Model
{
    protected $fillable = ['title', 'body', 'channel_id', 'user_id'];

    protected $with = ['channel'];

    protected static function boot()
    {
        parent::boot();


        static::addGlobalScope('replyCount', function (Builder $builder) {
            $builder->withCount('replies');
        });

        // TODO:
        // static::addGlobalScope('creator', function (Builder $builder) {
        //     $builder->with('creator');
        // });

        static::created(function ($thread) {
            $thread->recordActivity('created');
        });
    }

    /**
     * recordActivity
     *
     * @param  string $event_type
     *
     * @return void
     */
    public function recordActivity($event)
    {
        Activity::create([
            'user_id' => $this->activityOwner()->id,
            'type' => $this->getActivityType($event),
            'subject_id' => $this->id,
            'subject_type' => get_class($this)
        ]);
    }

    /**
     * getActivityType
     *
     * @param  string $event
     *
     * @return string
     */
    protected function getActivityType($event)
    {
        return strtolower((new \ReflectionClass($this))->getShortName()) . '_' . $event;
    }

    /**
     * activityOwner returns the logged in user 
     * or activity owner depending on Thread
     * @return User
     */
    protected function activityOwner()
    {
        if (auth()->check()) {
            return auth()->user();
        }

        return $this->creator;
    }

    /**
     * A thread may have many replies.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function replies()
    {
        // TODO: remove if TODO: counting works
        return $this->hasMany(Reply::class);
    }

    /**
     * A thread belongs to a creator.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * A thread is assigned a channel.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function channel()
    {
        return $this->belongsTo(Channel::class, 'channel_id');
    }

    /**
     * Add a reply to the thread.
     *
     * @param $reply
     */
    public function add_reply($reply)
    {
        $this->replies()->create($reply);
    }

    /**
     * Apply all relevant thread filters.
     *
     * @param  Builder       $query
     * @param  ThreadFilters $filters
     * @return Builder
     */
    public function scopeFilter($query, $filters)
    {
        return $filters->apply($query);
    }
}
