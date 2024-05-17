<?php

namespace PhanAn\Poddle\Exceptions;

class InvalidClosureElementException extends InvalidElementException
{
    protected function specUrl(): string
    {
        return 'https://github.com/Podcast-Standards-Project/PSP-1-Podcast-RSS-Specification#enclosure';
    }

    protected function elementName(): string
    {
        return '<enclosure>';
    }
}
