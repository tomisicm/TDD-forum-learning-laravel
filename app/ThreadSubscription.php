<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ThreadSubscription extends Model
{
    protected $fillable = ['thread_id', 'user_id'];
}
