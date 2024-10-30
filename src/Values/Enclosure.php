<?php

namespace PhanAn\Poddle\Values;

use Illuminate\Support\Arr;
use PhanAn\Poddle\Exceptions\InvalidClosureElementException;
use Saloon\XmlWrangler\Data\Element;
use Throwable;

class Enclosure extends Serializable
{
    public function __construct(
        public readonly string $url,
        public readonly string $type,
        public readonly int $length
    ) {
    }

    public static function fromXmlElement(Element $element): static
    {
        try {
            return new static(
                url: $element->getAttribute('url'),
                type: $element->getAttribute('type'),
                length: intval($element->getAttribute('length')),
            );
        } catch (Throwable $exception) {
            throw new InvalidClosureElementException($exception);
        }
    }

    public static function fromArray(array $data): static
    {
        return new static(
            url: Arr::get($data, 'url'),
            type: Arr::get($data, 'type'),
            length: Arr::get($data, 'length'),
        );
    }

    /** @return array<string, string|int> */
    public function toArray(): array
    {
        return [
            'url' => $this->url,
            'type' => $this->type,
            'length' => $this->length,
        ];
    }
}
