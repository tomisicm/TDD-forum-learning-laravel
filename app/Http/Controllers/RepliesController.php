<?php

namespace App\Http\Controllers;

use App\Thread;
use App\Reply;
use App\Channel;
use Illuminate\Http\Request;

class RepliesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Channel $channel, Thread $thread)
    {
        return $thread->replies()->paginate(5);
    }

    /**
     * Store a newly created reply in storage, if reply is valid.
     *
     * @param  App\Thread  $thread
     * @param  App\Inspections\Spam  $spam
     * 
     * @return \Illuminate\Http\Response
     */
    public function store(Thread $thread)
    {
        $this->validate(request(), [
            'body' => 'required|spamfree'
        ]);

        $attributes = [
            'body' => request('body'),
            'user_id' => auth()->id(),
            'thread_id' => $thread->id
        ];

        $thread->addReply($attributes);

        return $thread->replies;
    }

    /**
     * Store a newly created reply in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function update(Reply $reply)
    {
        $this->validate(request(), [
            'body' => 'required|spamfree'
        ]);

        $attributes = [
            'body' => request('body'),
        ];

        $reply->update($attributes);

        return $reply;
    }

    /**
     * Delete a newly created reply in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function destroy(Reply $reply)
    {
        $this->authorize('delete', $reply);
        $reply->delete();

        return response([], 204);
    }
}
