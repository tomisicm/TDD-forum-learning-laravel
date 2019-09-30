<?php

namespace App;


use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

use App\Events\ThreadWasUpdated;

class Thread extends Model
{
    use RecordsActivity;

    protected $fillable = ['title', 'body', 'channel_id', 'user_id'];

    protected $with = ['channel'];

    protected static function boot()
    {
        parent::boot();


        static::addGlobalScope('replyCount', function (Builder $builder) {
            $builder->withCount('replies');
        });

        static::deleting(function ($thread) {

            // TODO: research $thread->replies->each->delete();

            $thread->replies->each(
                function ($reply) {
                    $reply->delete();
                }
            );
        });

        // TODO:
        // static::addGlobalScope('creator', function (Builder $builder) {
        //     $builder->with('creator');
        // });
    }


    /**
     * A thread may have many replies
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function replies()
    {
        return $this->hasMany(Reply::class);
    }

    /**
     * A thread belongs to a creator
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * A thread is assigned a channel
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function channel()
    {
        return $this->belongsTo(Channel::class, 'channel_id');
    }

    /**
     * Add a reply to the thread
     * Create notifications for thread subscribers
     * @param $reply
     */
    public function addReply($reply)
    {
        $reply = $this->replies()->create($reply);

        event(new ThreadWasUpdated($this, $reply));

        return $reply;
    }

    /**
     * Apply all relevant thread filters
     * @param  Builder       $query
     * @param  ThreadFilters $filters
     * @return Builder
     */
    public function scopeFilter($query, $filters)
    {
        return $filters->apply($query);
    }

    /**
     * subscriptions
     * @return \Illuminate\Database\Eloquent\Relations\hasMany
     */
    public function subscriptions()
    {
        return $this->hasMany(ThreadSubscription::class);
    }

    /**
     * handleSubscribe - subscribe/unsubscribe user to the thread
     * @param  mixed $userId
     * @return void
     */
    public function notifySubscribers($reply)
    {
        $this->subscriptions
            ->where('user_id', '!=', $reply->user_id)
            ->each->notify($reply);
    }

    /**
     * handleSubscribe - subscribe/unsubscribe user to the thread
     * @param  mixed $userId
     * @return void
     */
    public function handleSubscribe($userId = null)
    {
        if (!$this->hasSubscription($userId ?: auth()->id())) {
            $this->subscribe($userId ?: auth()->id());
            return true;
        }
        $this->unsubscribe($userId ?: auth()->id());
        return false;
    }

    /**
     * hasSubscription - is user subscribed to the thread
     * @param  mixed $userId
     * @return boolean
     */
    public function hasSubscription($userId)
    {
        return $this->subscriptions()
            ->where('user_id', $userId)
            ->exists();
    }

    /**
     * subscribe
     * @param  mixed  $userId
     * @return  \App\Thread
     */
    public function subscribe($userId = null)
    {
        $this->subscriptions()->create([
            'thread_id' => $this->id,
            'user_id' => $userId ?: auth()->id()
        ]);

        return $this;
    }

    /**
     * unsubscribe
     * @param  mixed  $userId
     * @return  \App\Thread
     */
    public function unsubscribe($userId = null)
    {
        $this->subscriptions()
            ->where('user_id', $userId ?: auth()->id())
            ->delete();

        return $this;
    }

    public function getIsSubscribedToAttribute()
    {
        return $this->hasSubscription(auth()->id());
    }

    public function getSubscriptionsAttribute()
    {
        return $this->subscriptions()->count();
    }
}
