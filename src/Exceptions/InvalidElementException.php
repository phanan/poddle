<?php

namespace PhanAn\Poddle\Exceptions;

use Exception;
use Throwable;

abstract class InvalidElementException extends Exception
{
    abstract protected function specUrl(): string;
    abstract protected function elementName(): string;

    public function __construct(?Throwable $previous = null)
    {
        parent::__construct(
            sprintf(
                'Invalid %s element. A valid %s element must conform to the specifications at %s.',
                $this->elementName(),
                $this->elementName(),
                $this->specUrl()
            ),
            400,
            $previous
        );
    }
}
