<?php

namespace PhanAn\Poddle\Values;

use Illuminate\Support\LazyCollection;

/**
 * @template TKey of array-key
 * @template TModel of Episode
 * @extends LazyCollection<TKey, TModel>
 */
class EpisodeCollection extends LazyCollection
{
    public function __construct($source = null)
    {
        parent::__construct($source);
    }
}
