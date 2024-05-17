<?php

namespace PhanAn\Poddle\Exceptions;

class InvalidGuidElementException extends InvalidElementException
{
    protected function specUrl(): string
    {
        return 'https://github.com/Podcast-Standards-Project/PSP-1-Podcast-RSS-Specification#guid';
    }

    protected function elementName(): string
    {
        return '<guid>';
    }
}
