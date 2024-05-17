<?php

namespace PhanAn\Poddle\Values;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Contracts\Support\Jsonable;

abstract class Serializable implements Arrayable, Jsonable
{
    abstract public function toArray(): array;

    public function toJson($options = 0): false|string
    {
        return json_encode($this->toArray(), $options);
    }
}
