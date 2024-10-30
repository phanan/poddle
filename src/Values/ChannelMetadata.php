<?php

namespace PhanAn\Poddle\Values;

use Illuminate\Support\Arr;
use PhanAn\Poddle\Enums\PodcastType;

class ChannelMetadata extends Serializable
{
    public function __construct(
        public readonly bool $locked,
        public readonly ?string $guid,
        public readonly ?string $author,
        public readonly ?string $copyright,
        public readonly TxtCollection $txts,
        public readonly FundingCollection $fundings,
        public readonly ?PodcastType $type,
        public readonly bool $complete
    ) {
    }

    public static function fromArray(array $data): static
    {
        return new static(
            locked: Arr::get($data, 'locked', false),
            guid: Arr::get($data, 'guid'),
            author: Arr::get($data, 'author'),
            copyright: Arr::get($data, 'copyright'),
            txts: TxtCollection::fromArray(Arr::get($data, 'txts', [])),
            fundings: FundingCollection::fromArray(Arr::get($data, 'fundings', [])),
            type: PodcastType::tryFrom(Arr::get($data, 'type') ?? ''),
            complete: Arr::get($data, 'complete', false),
        );
    }

    /** @return array<string, mixed> */
    public function toArray(): array
    {
        return [
            'locked' => $this->locked,
            'guid' => $this->guid,
            'author' => $this->author,
            'copyright' => $this->copyright,
            'txts' => $this->txts->toArray(),
            'fundings' => $this->fundings->toArray(),
            'type' => $this->type?->value,
            'complete' => $this->complete,
        ];
    }
}
