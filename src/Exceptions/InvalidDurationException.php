<?php

namespace PhanAn\Poddle\Exceptions;

use Exception;

class InvalidDurationException extends Exception
{
    public function __construct(string $duration)
    {
        parent::__construct(
            "Invalid duration: $duration. Duration must be in [HH:]MM:SS format or as number of seconds."
        );
    }
}
