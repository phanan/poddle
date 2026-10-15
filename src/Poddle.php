<?php

namespace PhanAn\Poddle;

use DOMDocument;
use DOMElement;
use Generator;
use GuzzleHttp\Psr7\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use PhanAn\Poddle\Enums\PodcastType;
use PhanAn\Poddle\Values\CategoryCollection;
use PhanAn\Poddle\Values\Channel;
use PhanAn\Poddle\Values\ChannelMetadata;
use PhanAn\Poddle\Values\Episode;
use PhanAn\Poddle\Values\EpisodeCollection;
use PhanAn\Poddle\Values\FundingCollection;
use PhanAn\Poddle\Values\TxtCollection;
use Psr\Http\Client\ClientInterface;
use Saloon\XmlWrangler\Data\Element;
use Saloon\XmlWrangler\XmlReader;
use Throwable;
use VeeWee\Xml\Dom\Document;
use VeeWee\Xml\Dom\Traverser\Visitor\RemoveNamespaces;
use VeeWee\Xml\Dom\Xpath;
use VeeWee\Xml\Exception\RuntimeException as XmlRuntimeException;

use function VeeWee\Xml\Dom\Configurator\traverse;

class Poddle
{
    public readonly XmlReader $xmlReader;

    private ?Xpath $xpath = null;

    public function __construct(public readonly string $xml)
    {
        $this->xmlReader = XmlReader::fromString($xml);
    }

    public static function fromUrl(string $url, int $timeoutInSeconds = 30, ?ClientInterface $client = null): self
    {
        $xml = $client
            ? $client->sendRequest(new Request('GET', $url, ['timeout' => (string) $timeoutInSeconds]))->getBody()
            : Http::timeout($timeoutInSeconds)->get($url)->body();

        return new self((string) $xml);
    }

    public static function fromXml(string $xml): self
    {
        return new self($xml);
    }

    public function getChannel(): Channel
    {
        return new Channel(
            url: (string) $this->getSoleValue('atom:link@href'),
            title: (string) $this->getSoleValue('title'),
            description: (string) $this->getSoleValue('description'),
            link: (string) $this->getSoleValue('link'),
            language: (string) $this->getSoleValue('language'),
            categories: $this->getCategories(),
            explicit: $this->getSoleValue('itunes:explicit') === 'yes',
            image: (string) $this->getSoleValue('itunes:image@href'),
            metadata: $this->getMetadata(),
        );
    }

    public function getEpisodes(bool $ignoreInvalids = false): EpisodeCollection
    {
        return new EpisodeCollection(function () use ($ignoreInvalids): Generator {
            foreach ($this->xmlReader->element('rss.channel.item')->collectLazy() as $item) {
                try {
                    yield Episode::fromXmlElement($item);
                } catch (Throwable $e) {
                    if ($ignoreInvalids) {
                        continue;
                    }

                    throw $e;
                }
            }
        });
    }

    private function getSoleValue(string ...$queries): ?string
    {
        try {
            foreach ($queries as $query) {
                if (!Str::startsWith('/rss/channel/', $query)) {
                    $query = '/rss/channel/' . ltrim($query, '/');
                }

                if (Str::contains($query, '@')) {
                    [$query, $attribute] = explode('@', $query, 2);
                    $value = $this->queryElements($query)->first()?->getAttribute($attribute);
                } else {
                    $value = $this->queryElements($query)->first()?->getContent();
                }

                if ($value) {
                    return $value;
                }
            }

            return null;
        } catch (Throwable) {
            return null;
        }
    }

    private function getMetadata(): ChannelMetadata
    {
        return new ChannelMetadata(
            locked: $this->getSoleValue('podcast:locked') === 'yes',
            guid: $this->getSoleValue('podcast:guid'),
            author: $this->getSoleValue('itunes:author'),
            copyright: $this->getSoleValue('copyright'),
            txts: $this->getTxts(),
            fundings: $this->getFundings(),
            type: PodcastType::tryFrom($this->getSoleValue('itunes:type') ?? ''),
            complete: $this->getSoleValue('itunes:complete') === 'yes',
        );
    }

    private function getFundings(): FundingCollection
    {
        return FundingCollection::fromXmlElements(
            $this->getChannelElements('podcast:funding')
        );
    }

    private function getCategories(): CategoryCollection
    {
        return CategoryCollection::fromXmlElements(
            $this->getChannelElements('itunes:category')
        );
    }

    private function getTxts(): TxtCollection
    {
        return TxtCollection::fromXmlElements(
            $this->getChannelElements('podcast:txt')
        );
    }

    /** @return Collection<int, Element> */
    private function getChannelElements(string $name): Collection
    {
        try {
            return $this->queryElements("/rss/channel//{$name}[not(ancestor::{$name})]");
        } catch (XmlRuntimeException) {
            return collect();
        }
    }

    /** @return Collection<int, Element> */
    private function queryElements(string $query): Collection
    {
        return collect($this->getXpath()->query($query))
            ->whereInstanceOf(DOMElement::class)
            ->map(static fn (DOMElement $node): Element => self::toXmlElement($node))
            ->values();
    }

    private function getXpath(): Xpath
    {
        $this->xpath ??= Document::fromXmlString($this->xml, traverse(RemoveNamespaces::unprefixed()))->xpath();

        return $this->xpath;
    }

    private static function toXmlElement(DOMElement $node): Element
    {
        $document = new DOMDocument();
        $document->appendChild($document->importNode($node, true));

        return Arr::first(XmlReader::fromString((string) $document->saveXML())->elements());
    }
}
