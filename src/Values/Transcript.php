<?php

namespace PhanAn\Poddle\Values;

use Illuminate\Support\Arr;
use PhanAn\Poddle\Exceptions\InvalidTranscriptElementException;
use Saloon\XmlWrangler\Data\Element;
use Throwable;

class Transcript extends Serializable
{
    final public function __construct(
        public readonly string $url,
        public readonly string $type,
        public readonly ?string $language,
        public readonly ?string $rel
    ) {
    }

    public static function fromXmlElement(Element $element): static
    {
        try {
            return new static(
                url: $element->getAttribute('url'),
                type: $element->getAttribute('type'),
                language: $element->getAttribute('language'),
                rel: $element->getAttribute('rel'),
            );
        } catch (Throwable $exception) {
            throw new InvalidTranscriptElementException($exception);
        }
    }

    public static function fromArray(array $data): static
    {
        return new static(
            url: Arr::get($data, 'url'),
            type: Arr::get($data, 'type'),
            language: Arr::get($data, 'language'),
            rel: Arr::get($data, 'rel'),
        );
    }

    /** @return array<string, string|null> */
    public function toArray(): array
    {
        return [
            'url' => $this->url,
            'type' => $this->type,
            'language' => $this->language,
            'rel' => $this->rel,
        ];
    }
}
