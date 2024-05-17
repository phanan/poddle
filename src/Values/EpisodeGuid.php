<?php

namespace PhanAn\Poddle\Values;

use Illuminate\Support\Arr;
use PhanAn\Poddle\Exceptions\InvalidGuidElementException;
use Saloon\XmlWrangler\Data\Element;
use Throwable;

class EpisodeGuid extends Serializable
{
    final public function __construct(public readonly string $value, public readonly bool $isPermaLink)
    {
    }

    public static function fromXmlElement(Element $element): static
    {
        try {
            return new static(
                value: $element->getContent(),
                isPermaLink: $element->getAttribute('isPermaLink') === 'true',
            );
        } catch (Throwable $exception) {
            throw new InvalidGuidElementException($exception);
        }
    }

    public static function fromArray(array $data): static
    {
        return new static(
            value: Arr::get($data, 'guid'),
            isPermaLink: Arr::get($data, 'is_perma_link'),
        );
    }

    /** @return array<string, string|bool> */
    public function toArray(): array
    {
        return [
            'value' => $this->value,
            'is_perma_link' => $this->isPermaLink,
        ];
    }

    public function __toString(): string
    {
        return $this->value;
    }
}
