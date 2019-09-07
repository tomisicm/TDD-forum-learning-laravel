<?php

namespace App\Filters;

use Illuminate\Http\Request;
use App\User;


class ThreadFilters extends Filters
{

    protected $filters = ['by', 'popular', 'unreplied'];


    /**
     * Filter the query by a given username
     *
     * @param string $username
     *
     * @return Builder
     */
    protected function by($username)
    {
        $user = User::where('name', $username)->firstOrFail();

        return $this->builder->where('user_id', $user->id);
    }

    /**
     * Filter the query according to the most popular threads
     *
     * @return Builder
     */
    protected function popular()
    {
        $this->builder->getQuery()->orders = [];

        return $this->builder->orderBy('replies_count', 'desc');
    }

    protected function unreplied()
    {
        return $this->builder->doesntHave('replies');
    }
}
