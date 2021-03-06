<?php

namespace App\Http\Controllers;

use App\Channel;
use App\Thread;
use App\Filters\ThreadFilters;
use App\User;

use Illuminate\Http\Request;

class ThreadController extends Controller
{
    /**
     * index
     *
     * @param  mixed $channel Channel
     *
     * @return void
     */
    public function index(Channel $channel, ThreadFilters $filters)
    {
        $threads = $this->getThreads($channel, $filters)->with('creator')->get();

        return $threads;
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, Channel $channel)
    {
        $attributes = request()->validate([
            'title' => 'required|min:3',
            'body' => 'required|max:512|spamfree'
        ]);
        $attributes['user_id'] = auth()->id();
        $attributes['channel_id'] = $channel->id;

        return Thread::create($attributes);
    }

    /**
     * Update the specified resource in storage.
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Thread  $thread
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Thread $thread)
    {
        $attributes = request()->validate([
            'title' => 'required|min:3',
            'body' => 'required|max:512|spamfree'
        ]);

        $thread->update($attributes);

        return $thread;
    }

    /**
     * Display the specified resource.
     *
     * @param  mixed $channel
     * @param  mixed $thread
     *
     * @return void
     */
    public function show(Channel $channel, Thread $thread)
    {
        return $thread->load('creator')
            ->append('isSubscribedTo')
            ->append('subscriptionsCount');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Thread  $thread
     * @return \Illuminate\Http\Response
     */
    public function edit(Thread $thread)
    {
        //
    }


    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Thread  $thread
     * @return \Illuminate\Http\Response
     */
    public function destroy(Channel $channel, Thread $thread)
    {
        $this->authorize('update', $thread);

        $thread->delete();
        return response([], 204);
    }

    /**
     * Fetch all relevant threads.
     *
     * @param Channel       $channel
     * @param ThreadFilters $filters
     * @return mixed
     */
    protected function getThreads(Channel $channel, ThreadFilters $filters)
    {
        $threads = Thread::latest()->filter($filters);

        if ($channel->exists) {
            $threads->where('channel_id', $channel->id);
        }

        return $threads;
    }
}
