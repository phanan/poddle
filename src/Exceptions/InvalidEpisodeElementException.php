<?php

namespace PhanAn\Poddle\Exceptions;

class InvalidEpisodeElementException extends InvalidElementException
{
    protected function specUrl(): string
    {
        return 'https://github.com/Podcast-Standards-Project/PSP-1-Podcast-RSS-Specification#required-item-elements';
    }

    protected function elementName(): string
    {
        return '<item>';
    }
}
