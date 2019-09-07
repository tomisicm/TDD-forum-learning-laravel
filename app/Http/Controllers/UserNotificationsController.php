<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use App\Notifications\ThreadWasUpdated;

class UserNotificationsController extends Controller
{
    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Notifications\ThreadWasUpdated $notification
     * @return \Illuminate\Http\Response
     */
    public function destroy(User $user, $notification)
    {
        auth()->user()->notifications()->findOrFail($notification)->markAsRead();
    }
}
