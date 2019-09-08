<?php

namespace App\Inspections;

use Exception;

class RepeatingCharacters
{

    public function detect($body)
    {
        if (preg_match('/(.)\\1{4,}/', $body)) {
            throw new \Exception('Reply contains spam!');
        }

        return false;
    }
}
