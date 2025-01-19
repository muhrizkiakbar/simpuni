<?php

namespace App\Exceptions;

use Exception;

class DenunciationException extends Exception
{
    public function __construct($message = "Denunciation can not be update.")
    {
        parent::__construct($message);
    }
}

