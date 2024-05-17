<?php

namespace PhanAn\Poddle\Values;

use Illuminate\Support\Arr;
use PhanAn\Poddle\Exceptions\InvalidTxtElementException;
use Saloon\XmlWrangler\Data\Element;
use Throwable;

class Txt extends Serializable
{
    final public function __construct(public readonly string $content, public readonly ?string $purpose)
    {
    }

    public static function fromXmlElement(Element $element): static
    {
        try {
            return new static(
                content: $element->getContent(),
                purpose: $element->getAttribute('purpose'),
            );
        } catch (Throwable $exception) {
            throw new InvalidTxtElementException($exception);
        }
    }

    public static function fromArray(array $data): static
    {
        return new static(
            content: Arr::get($data, 'content'),
            purpose: Arr::get($data, 'purpose'),
        );
    }

    /**
     * @return array<string, string|null>
     */
    public function toArray(): array
    {
        return [
            'content' => $this->content,
            'purpose' => $this->purpose,
        ];
    }
}
