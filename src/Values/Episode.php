<?php

namespace PhanAn\Poddle\Values;

use Illuminate\Support\Arr;
use PhanAn\Poddle\Exceptions\InvalidEpisodeElementException;
use Saloon\XmlWrangler\Data\Element;
use Throwable;

class Episode extends Serializable
{
    public function __construct(
        public readonly string $title,
        public readonly EpisodeGuid $guid,
        public readonly Enclosure $enclosure,
        public readonly EpisodeMetadata $metadata
    ) {
    }

    public static function fromArray(array $data): static
    {
        return new static(
            title: Arr::get($data, 'title'),
            guid: EpisodeGuid::fromArray(Arr::get($data, 'guid')),
            enclosure: Enclosure::fromArray(Arr::get($data, 'enclosure')),
            metadata: EpisodeMetadata::fromArray(Arr::get($data, 'metadata', [])),
        );
    }

    public static function fromXmlElement(Element $item): static
    {
        try {
            /** @var array<string, Element> $content */
            $content = $item->getContent();

            return new static(
                title: Arr::get($content, 'title', Arr::get($content, 'itunes:title'))->getContent(),
                guid: EpisodeGuid::fromXmlElement(Arr::get($content, 'guid')),
                enclosure: Enclosure::fromXmlElement(Arr::get($content, 'enclosure')),
                metadata: EpisodeMetadata::fromXmlElement($item)
            );
        } catch (Throwable $exception) {
            throw new InvalidEpisodeElementException($exception);
        }
    }

    /** @return array<string, mixed> */
    public function toArray(): array
    {
        return [
            'title' => $this->title,
            'guid' => $this->guid->toArray(),
            'enclosure' => $this->enclosure->toArray(),
            'metadata' => $this->metadata->toArray(),
        ];
    }
}
