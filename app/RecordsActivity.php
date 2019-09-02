<?php

namespace App;

trait RecordsActivity
{
    /**
     * recordActivity - creates Activity record
     * @param  string $event_type
     *
     * @return void
     */
    public function recordActivity($event)
    {
        Activity::create([
            'user_id' => $this->activityOwner()->id,
            'type' => $this->getActivityType($event),
            'subject_id' => $this->id,
            'subject_type' => get_class($this)
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
}
