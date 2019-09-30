<?php

namespace App\Http\Controllers;

use App\Channel;
use App\Thread;
use App\ThreadSubscription;

class SubscriptionsController extends Controller
{
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Channel $channel, Thread $thread)
    {
        $isSubscribed = $thread->handleSubscribe(auth()->id());

        return response([$isSubscribed], 200);
    }
}
