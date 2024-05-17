<?php

namespace PhanAn\Poddle\Exceptions;

use Exception;

class InvalidDateTimeFormatException extends Exception
{
    public function __construct(string $datetime)
    {
        parent::__construct("Invalid datetime format: $datetime. Make sure the value conforms to RFC 2822 specs.");
    }
}
