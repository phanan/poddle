<?php

namespace PhanAn\Poddle\Exceptions;

class InvalidFundingElementException extends InvalidElementException
{
    protected function specUrl(): string
    {
        return 'https://github.com/Podcast-Standards-Project/PSP-1-Podcast-RSS-Specification#channel-podcast-funding';
    }

    protected function elementName(): string
    {
        return '<podcast:funding>';
    }
}
