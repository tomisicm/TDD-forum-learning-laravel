<?php

namespace App\Inspections;

use Exception;

class InvalidKeywords
{
    protected $invalidKeywords = [
        'ITS SPAM'
    ];

    /**
     * detect - Throw exception if there is spam, else false
     * @param  String $body
     * @return  mixed
     */
    public function detect($body)
    {
        foreach ($this->invalidKeywords as $keyword) {

            if (stripos($body, $keyword) !== false) {
                throw new Exception('Reply contains spam!');
            }
        }

        return false;
    }
}
