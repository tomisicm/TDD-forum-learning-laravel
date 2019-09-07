<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

use App\Notifications\ThreadWasUpdated;

class ThreadSubscription extends Model
{
    protected $fillable = ['thread_id', 'user_id'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function notify($reply)
    {
        $this->user->notify(new ThreadWasUpdated($reply->thread, $reply));
    }
}
