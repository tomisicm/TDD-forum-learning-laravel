<?php

namespace App;

class Spam
{
    /**
     * detect - Throw exception if there is spam, else false
     * @param  String $body
     * @return  mixed
     */
    public function detect($body)
    {
        $this->detectInvalidKeywords($body);
        $this->detectRepeatingCharacters($body);

        return false;
    }

    protected function detectInvalidKeywords($body)
    {
        $invalidKeywords = [
            'ITS SPAM'
        ];

        foreach ($invalidKeywords as $keyword) {

            if (stripos($body, $keyword) !== false) {
                throw new \Exception('Reply contains spam!');
            }
        }
    }

    protected function detectRepeatingCharacters($body) 
    {
        if (preg_match('/(.)\\1{4,}/', $body)) {
            throw new \Exception('Reply contains spam!');
        }
    }
}
