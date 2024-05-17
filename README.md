# Poddle – PHP Podcast Feed Parser [![Unit Tests](https://github.com/phanan/poddle/actions/workflows/unit.yml/badge.svg)](https://github.com/phanan/poddle/actions/workflows/unit.yml)

![Poddle](./assets/banner.webp)

> Effortlessly parse podcast feeds in PHP following [PSP-1 Podcast RSS Standard](https://github.com/Podcast-Standards-Project/PSP-1-Podcast-RSS-Specification).

## Requirements and Installation

Poddle requires PHP 8.1 or higher. You can install the library via Composer by running the following command:

```bash
composer require phanan/poddle
```

## Usage

To parse a podcast feed, call the `fromUrl` method with the feed URL:

```php
$poddle = \PhanAn\Poddle::fromUrl('https://example.com/feed.xml');
```

It is possible to configure the timeout value for the request by passing an integer as the second parameter for `Poddle::fromUrl`.

For total control, you can make the request yourself (for example using an HTTP client) and pass the response body to `Poddle::fromXml` instead:

```php
use Illuminate\Support\Facades\Http;

$poddle = \PhanAn\Poddle::fromXml(Http::timeout(60)
    ->withoutVerifying()
    ->get('https://example.com/feed.xml')
    ->body()
);
```

Upon success, both `fromUrl` and `fromXml` methods return a `Poddle` object, which you can use to access the feed's channel and episodes.

### Channel

To access the podcast channel, call `getChannel` on the `Poddle` object:

```php
/** @var \PhanAn\Poddle\Values\Channel $channel */
$channel = $poddle->getChannel();
```

All channel's [required elements](https://github.com/Podcast-Standards-Project/PSP-1-Podcast-RSS-Specification#required-channel-elements) per the PSP-1 standard are available as properties on the `Channel` object:

```php
$channel->title; // string
$channel->link; // string
$channel->description; // string
$channel->language; // string
$channel->image; // string
$channel->categories; // \PhanAn\Poddle\Values\CategoryCollection<\PhanAn\Poddle\Values\Category>
$channel->explicit; // bool
```

All channel’s [recommended elements](https://github.com/Podcast-Standards-Project/PSP-1-Podcast-RSS-Specification#recommended-channel-elements) are available via the `metadata` property:

```php
$channel->metadata; // \PhanAn\Poddle\Values\ChannelMetadata
$channel->metadata->locked; // bool
$channel->metadata->guid; // ?string
$channel->metadata->author; // ?string
$channel->metadata->copyright; // ?string
$channel->metadata->txts; // \PhanAn\Poddle\Values\TxtCollection<\PhanAn\Poddle\Values\Txt>
$channel->metadata->fundings; // \PhanAn\Poddle\Values\FundingCollection<\PhanAn\Poddle\Values\Funding>
$channel->metadata->type; // ?\PhanAn\Poddle\Values\PodcastType
$channel->metadata->complete; // bool
```

### Episodes

To access the podcast episodes, call `getEpisodes` on the `Poddle` object:

```php
$episodes = $poddle->getEpisodes();
```

This method returns a [lazy collection](https://laravel.com/docs/11.x/collections#lazy-collections) of `\PhanAn\Poddle\Values\Episode` objects. You can iterate over the collection to access each episode:

```php
$episodes->each(function (\PhanAn\Poddle\Values\Episode $episode) {
    // Access episode properties
});
```

All episode's [required elements](https://github.com/Podcast-Standards-Project/PSP-1-Podcast-RSS-Specification#required-item-elements) per the PSP-1 standard are available as properties on the `Episode` object:

```php
$episode->title; // string
$episode->enclosure; // \PhanAn\Poddle\Values\Enclosure
$episode->guid; // \PhanAn\Poddle\Values\EpisodeGuid
```

All episode's [recommended elements](https://github.com/Podcast-Standards-Project/PSP-1-Podcast-RSS-Specification#recommended-item-elements) are available via the `metadata` property:

```php
$episode->metadata; // \PhanAn\Poddle\Values\EpisodeMetadata
$episode->metadata->link; // ?string
$episode->metadata->pubDate; // ?\DateTime
$episode->metadata->description; // ?string
$episode->metadata->duration; // ?int
$episode->metadata->image; // ?string
$episode->metadata->explicit; // ?bool
$episode->metadata->transcripts; // \PhanAn\Poddle\Values\TranscriptCollection<\PhanAn\Poddle\Values\Transcript>
$episode->metadata->episode; // ?int
$episode->metadata->season; // ?int
$episode->metadata->type; // ?\PhanAn\Poddle\Values\EpisodeType
$episode->metadata->block; // ?bool
```

### Other Elements and Values

If you need to access other elements or values not covered by the PSP-1 standard, you can make use of the `$xmlReader` property on the `Poddle` object:

```php
$xmlReader = $poddle->xmlReader;
```

This property is an instance of `Saloon\XmlWrangler\XmlReader` and allows you to navigate the XML document directly. For example, to access the feed's `lastBuildDate` value:

```php
$poddle = \PhanAn\Poddle::fromUrl('https://example.com/feed.xml');
$poddle->xmlReader->value('rss.channel.lastBuildDate')?->sole(); // 'Thu, 02 May 2024 06:44:38 +0000'
```

For more information on how to use `XmlReader`, refer to [Saloon\XmlWrangler documentation](https://github.com/saloonphp/xml-wrangler).

The original feed content is available via the `xml` property on the `Poddle` object:

```php
$xml = $poddle->xml; // string
```

## Serialization and Deserialization

All classes under the `PhanAn\Poddle\Values` namespace implement the [`\Illuminate\Contracts\Support\Arrayable`](https://laravel.com/api/11.x/Illuminate/Contracts/Support/Arrayable.html)
and [`\Illuminate\Contracts\Support\Jsonable`](https://laravel.com/api/11.x/Illuminate/Contracts/Support/Jsonable.html) contracts, which provide two methods:

```php
/**
  * Get the instance as an array. All nested objects are also converted to arrays.
  */
public function toArray(): array;

/**
  * Convert the object to its JSON representation.
  */
public function toJson($options = 0): string;
```

Additionally, classes like `Channel` and `Episode` provide `fromArray` static methods to create instances from arrays.
These methods allow you to easily serialize and deserialize the objects, making it straightforward to store and retrieve the data in a database or JSON file.
For instance, you can create an Eloquent [custom cast](https://laravel.com/docs/11.x/eloquent-mutators#custom-casts) in Laravel this way:

```php
use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use PhanAn\Poddle\Values\Channel;

class ChannelCast implements CastsAttributes
{
    public function get($model, string $key, $value, array $attributes): Channel
    {
        return Channel::fromArray(json_decode($value, true));
    }

    /** @param Channel $value */
    public function set($model, string $key, $value, array $attributes)
    {
        return $value->toJson();
    }
}
```

Then, you can use the cast in your Eloquent model:

```php
use Illuminate\Database\Eloquent\Model;

class Podcast extends Model
{
    protected $casts = [
        'channel' => ChannelCast::class,
    ];
}
```

## Possible Questions

### Why does Poddle not include element or value X from the feed?

Poddle follows the PSP-1 standard, which specifies the required and recommended elements for a podcast feed.
If an element or value is not part of the standard, it is not included in Poddle. However, you can still access any element or value using the `xmlReader` property as described above.

### How come `pubDate` is not a required element for episodes?

The PSP-1 standard does not require `pubDate` for episodes, but it is a recommended element.
As a result, `pubDate` is available as part of the episode's metadata as a nullable `\DateTime` object.
It’s up to you to determine if the value always presents and design your system accordingly.

### Why is the episode's GUID an object instead of a string?

Per PSP-1 standard, an item’s `<guid>` element indeed contains a globally unique string value, but it can also have an attribute `isPermaLink` that indicates whether the GUID is a permalink.
As such, the item GUID in Poddle is represented as an object with two public properties: `value` (string) and `isPermaLink` (bool).
The object, however, implements the `__toString` method, so you can cast it to a string for convenience.

### Where is an episode’s media URL?

The media URL for an episode is available as part of the episode's `enclosure` property, along with the length (in seconds) and media type.

### Can you support feature X/Y/Z?

Poddle aims to be a lightweight and efficient podcast feed parser that follows the PSP-1 standard, not a full-blown RSS/Atom parser.
That said, if you have a feature request or suggestion, feel free to [open an issue](https://github.com/phanan/poddle/issues/new).
Better yet, you can fork the repository, implement the feature yourself, and submit a pull request.
