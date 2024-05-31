<?php

namespace Tests;

use GuzzleHttp\Psr7\Utils;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\LazyCollection;
use Mockery;
use PhanAn\Poddle\Poddle;
use PhanAn\Poddle\Values\Channel;
use PhanAn\Poddle\Values\Episode;
use PhanAn\Poddle\Values\EpisodeCollection;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

class PoddleTest extends TestCase
{
    public function testGetChannel(): void
    {
        $channel = Poddle::fromXml(file_get_contents(__DIR__ . '/fixtures/sample.xml'))->getChannel();
        self::assertChannel($channel);
    }

    public function testGetEpisodes(): void
    {
        $episodes = Poddle::fromXml(file_get_contents(__DIR__ . '/fixtures/sample.xml'))->getEpisodes();
        self::assertEpisodes($episodes);
    }

    public function testParseUrl(): void
    {
        Http::fake([
            'https://mypodcast.com/feed' => Http::response(file_get_contents(__DIR__ . '/fixtures/sample.xml'))
        ]);

        $parser = Poddle::fromUrl('https://mypodcast.com/feed');

        self::assertChannel($parser->getChannel());
        self::assertEpisodes($parser->getEpisodes());
    }

    public function testParseUrlUsingClientInterface(): void
    {
        $clientMock = Mockery::mock(ClientInterface::class);

        $responseMock = Mockery::mock(ResponseInterface::class, [
            'getBody' => Utils::streamFor(file_get_contents(__DIR__ . '/fixtures/sample.xml')),
        ]);

        $clientMock->shouldReceive('sendRequest')->with(
            Mockery::on(static function (RequestInterface $request): bool {
                self::assertSame('GET', $request->getMethod());
                self::assertSame('https://mypodcast.com/feed', (string) $request->getUri());
                self::assertSame('45', $request->getHeader('timeout')[0]);

                return true;
            })
        )->andReturn($responseMock);

        $parser = Poddle::fromUrl(
            url: 'https://mypodcast.com/feed',
            timeoutInSeconds: 45,
            client: $clientMock
        );

        self::assertChannel($parser->getChannel());
        self::assertEpisodes($parser->getEpisodes());
    }

    private static function assertChannel(Channel $channel): void
    {
        self::assertEquals([
            'url' => 'https://phanan.net',
            'title' => 'Podcast Feed Parser',
            'description' => 'Parse podcast feeds with PHP following PSP-1 Podcast RSS Standard',
            'link' => 'https://github.com/phanan/podcast-feed-parser',
            'language' => 'en-US',
            'categories' => [
                [
                    'text' => 'News',
                    'sub_category' => [
                        'text' => 'Tech News',
                        'sub_category' => null,
                    ],
                ],
            ],
            'explicit' => false,
            'image' => 'https://github.com/phanan.png',
            'metadata' => [
                'locked' => false,
                'guid' => null,
                'author' => 'Phan An (phanan)',
                'copyright' => 'Phan An © 2024',
                'txts' => [
                    [
                        'content' => 'naj3eEZaWVVY9a38uhX8FekACyhtqP4JN',
                        'purpose' => null,
                    ],
                    [
                        'content' => 'S6lpp-7ZCn8-dZfGc-OoyaG',
                        'purpose' => 'verify',
                    ],
                ],
                'fundings' => [
                    [
                        'url' => 'https://github.com/sponsors/phanan',
                        'text' => 'Sponsor me on GitHub',
                    ],
                    [
                        'url' => 'https://opencollective.com/koel',
                        'text' => 'My other Open Collective',
                    ],
                ],
                'type' => 'episodic',
                'complete' => false,
            ],
        ], $channel->toArray());
    }

    private function assertEpisodes(EpisodeCollection $episodes): void
    {
        self::assertInstanceOf(LazyCollection::class, $episodes);
        self::assertSame(8, $episodes->count());

        /** @var Episode $firstEpisode */
        $firstEpisode = $episodes->first();

        self::assertEquals([
            'title' => 'Hiking Treks Trailer',
            'guid' => [
                'value' => 'D03EEC9B-B1B4-475B-92C8-54F853FA2A22',
                'is_perma_link' => false,
            ],
            'enclosure' => [
                'url' => 'http://example.com/podcasts/everything/AllAboutEverythingEpisode4.mp3',
                'type' => 'audio/mpeg',
                'length' => 498537,
            ],
            'metadata' => [
                'link' => null,
                'pub_date' => 'Tue, 08 Jan 2019 01:15:00 +0000',
                'description' => 'The Sunset Explorers share tips, techniques and recommendations for great hikes',
                'duration' => 1079,
                'image' => null,
                'explicit' => false,
                'transcripts' => [],
                'episode' => null,
                'season' => null,
                'type' => 'trailer',
                'block' => false,
            ]
        ], $firstEpisode->toArray());
    }
}
