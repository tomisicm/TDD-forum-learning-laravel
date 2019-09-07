<?php

namespace App\Listeners;

use App\Events\ThreadWasUpdated;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class NotifyThreadSubscribers
{

    /**
     * Handle the event.
     *
     * @param  ThreadWasUpdated  $event
     * @return void
     */
    public function handle(ThreadWasUpdated $event)
    {
        $event->thread->subscriptions
            ->where('user_id', '!=', $event->reply->user_id)
            ->each
            ->notify($event->reply);
    }
}
