<?php

namespace App;

trait RecordsActivity
{
    protected $events = ['created'];

    protected static function bootRecordsActivity()
    {
        static::created(function ($thread) {
            $thread->recordActivity('created');
        });
    }

    /**
     * recordActivity - creates Activity record
     * @param  string $event_type
     *
     * @return void
     */
    public function recordActivity($event)
    {
        $this->activity()->create([
            'user_id' => $this->activityOwner()->id,
            'type' => $this->getActivityType($event)
        ]);
    }

    /**
     * getActivityType
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
     * A activity is assigned a model
     * @return \Illuminate\Database\Eloquent\Relations\MorphMany
     */
    public function activity()
    {
        return $this->morphMany(Activity::class, 'subject');
    }
}
