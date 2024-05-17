<?php

namespace PhanAn\Poddle\Exceptions;

class InvalidCategoryElementException extends InvalidElementException
{
    protected function specUrl(): string
    {
        return 'https://github.com/Podcast-Standards-Project/PSP-1-Podcast-RSS-Specification#itunescategory';
    }

    protected function elementName(): string
    {
        return '<itunes:category>';
    }
}
