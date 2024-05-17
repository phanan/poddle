<?php

namespace PhanAn\Poddle\Exceptions;

class InvalidTranscriptElementException extends InvalidElementException
{
    protected function specUrl(): string
    {
        return 'https://github.com/Podcast-Standards-Project/PSP-1-Podcast-RSS-Specification#podcasttranscript';
    }

    protected function elementName(): string
    {
        return '<podcast:transcript>';
    }
}
