<?php

namespace App\Inspections;

use App\Inspections\InvalidKeywords;
use App\Inspections\RepeatingCharacters;

class Spam
{

    protected $inspections = [
        InvalidKeywords::class,
        RepeatingCharacters::class
    ];

    /**
     * detect - Throw exception if there is spam, else false
     * @param  String $body
     * @return  mixed
     */
    public function detect($body)
    {
        foreach ($this->inspections as $inspection) {
            app($inspection)->detect($body);
        }

        return false;
    }
}
