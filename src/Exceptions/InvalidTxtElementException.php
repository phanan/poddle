<?php

namespace PhanAn\Poddle\Exceptions;

class InvalidTxtElementException extends InvalidElementException
{
    protected function specUrl(): string
    {
        return 'https://github.com/Podcast-Standards-Project/PSP-1-Podcast-RSS-Specification#podcasttxt';
    }

    protected function elementName(): string
    {
        return '<podcast:txt>';
    }
}
