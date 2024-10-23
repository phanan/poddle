<?php

namespace PhanAn\Poddle\Values;

use DateTime;
use DateTimeInterface;
use Illuminate\Support\Arr;
use PhanAn\Poddle\Enums\EpisodeType;
use PhanAn\Poddle\Exceptions\InvalidDateTimeFormatException;
use PhanAn\Poddle\Exceptions\InvalidDurationException;
use Saloon\XmlWrangler\Data\Element;

class EpisodeMetadata extends Serializable
{
    final public function __construct(
        public readonly ?string $link,
        public readonly ?DateTime $pubDate,
        public readonly ?string $description,
        public readonly ?int $duration,
        public readonly ?string $image,
        public readonly ?bool $explicit,
        public readonly TranscriptCollection $transcripts,
        public readonly ?int $episode,
        public readonly ?int $season,
        public readonly ?EpisodeType $type,
        public readonly ?bool $block,
    ) {
    }

    public static function fromXmlElement(Element $item): static
    {
        /** @var array<string, Element> $content */
        $content = $item->getContent();

        return new static(
            link: Arr::get($content, 'link')?->getContent(),
            pubDate: self::parseDateTime(Arr::get($content, 'pubDate')?->getContent()),
            description: Arr::get($content, 'description')?->getContent(),
            duration: self::parseDuration(Arr::get($content, 'itunes:duration')?->getContent()),
            image: Arr::get($content, 'itunes:image')?->getAttribute('href'),
            explicit: Arr::get($content, 'itunes:explicit')?->getContent() === 'true',
            transcripts: self::getTranscripts(Arr::get($content, 'podcast:transcript')),
            episode: optional(Arr::get($content, 'itunes:episode')?->getContent(), 'intval'),
            season: optional(Arr::get($content, 'itunes:season')?->getContent(), 'intval'),
            type: EpisodeType::tryFrom(Arr::get($content, 'itunes:episodeType')?->getContent() ?? ''),
            block: Arr::get($content, 'itunes:block')?->getContent() === 'yes',
        );
    }

    public static function fromArray(array $data): static
    {
        return new static(
            link: Arr::get($data, 'link'),
            pubDate: self::parseDateTime(Arr::get($data, 'pub_date')),
            description: Arr::get($data, 'description'),
            duration: self::parseDuration(Arr::get($data, 'duration')),
            image: Arr::get($data, 'image'),
            explicit: Arr::get($data, 'explicit'),
            transcripts: TranscriptCollection::fromArray(Arr::get($data, 'transcripts', [])),
            episode: optional(Arr::get($data, 'episode'), 'intval'),
            season: optional(Arr::get($data, 'season'), 'intval'),
            type: EpisodeType::tryFrom(Arr::get($data, 'type') ?? ''),
            block: optional(
                Arr::get($data, 'block'),
                static fn ($value) => filter_var($value, FILTER_VALIDATE_BOOLEAN)
            ),
        );
    }

    private static function getTranscripts(Element|array|null $value): TranscriptCollection
    {
        if (!$value) {
            return TranscriptCollection::make();
        }

        if ($value instanceof Element) {
            $content = $value->getContent();

            return is_array($content)
                ? TranscriptCollection::fromXmlElements($content)
                : TranscriptCollection::fromXmlElements([$value]);
        }

        return TranscriptCollection::fromXmlElements($value);
    }

    private static function parseDateTime(?string $input): ?DateTime
    {
        $formatted = $input ? DateTime::createFromFormat(DateTimeInterface::RFC2822, $input) : null;

        if ($formatted === false) {
            throw new InvalidDateTimeFormatException($input);
        }

        return $formatted;
    }

    private static function parseDuration(string|int|null $duration): ?int
    {
        $duration = (string) $duration;

        return $duration
            ? match (sscanf($duration, '%d:%d:%d', $x, $y, $z)) {
                1 => $x,
                2 => $x * 60 + $y,
                3 => $x * 3600 + $y * 60 + $z, // @phpstan-ignore-line
                default => throw new InvalidDurationException($duration),
            }
        : null;
    }

    /** @return array<string, mixed> */
    public function toArray(): array
    {
        return [
            'link' => $this->link,
            'pub_date' => $this->pubDate?->format(DateTimeInterface::RFC2822),
            'description' => $this->description,
            'duration' => $this->duration,
            'image' => $this->image,
            'explicit' => $this->explicit,
            'transcripts' => $this->transcripts->toArray(),
            'episode' => $this->episode,
            'season' => $this->season,
            'type' => $this->type?->value,
            'block' => $this->block,
        ];
    }
}
