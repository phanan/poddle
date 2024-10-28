<?php

namespace PhanAn\Poddle\Values;

use Illuminate\Support\Arr;
use PhanAn\Poddle\Exceptions\InvalidFundingElementException;
use Saloon\XmlWrangler\Data\Element;
use Throwable;

class Funding extends Serializable
{
    public function __construct(public readonly string $url, public readonly string $text)
    {
    }

    public static function fromXmlElement(Element $element): static
    {
        try {
            return new static(
                url: $element->getAttribute('url'),
                text: $element->getContent(),
            );
        } catch (Throwable $exception) {
            throw new InvalidFundingElementException($exception);
        }
    }

    public static function fromArray(array $data): static
    {
        return new static(
            url: Arr::get($data, 'url'),
            text: Arr::get($data, 'text'),
        );
    }

    /** @return array<string, string> */
    public function toArray(): array
    {
        return [
            'url' => $this->url,
            'text' => $this->text,
        ];
    }
}
