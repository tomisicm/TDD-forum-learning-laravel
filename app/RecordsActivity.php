<?php

namespace App;

trait RecordsActivity
{
    protected $events = ['created'];

    protected static function bootRecordsActivity()
    {
        /*
         * testing purposes only
         * during testing i usually do not have authenticated user
         * 
        
        if (auth()->user()) {
            return;
        }
        */

        foreach (static::getDefaultActivitiesToRecord() as $event) {

            static::$event(function ($model) use ($event) {
                $model->recordActivity($event);
            });
        };
    }

    /**
     * getDefaultActivitiesToRecord - returns  
     * array of default events to be registered
     *
     * @return Array
     */
    protected static function getDefaultActivitiesToRecord()
    {
        return ['created'];
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
