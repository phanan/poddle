<?php

namespace Tests\Values;

use PhanAn\Poddle\Exceptions\InvalidGuidElementException;
use PhanAn\Poddle\Values\EpisodeGuid;
use Saloon\XmlWrangler\Data\Element;
use Tests\TestCase;

class EpisodeGuidTest extends TestCase
{
    public function testCreateFromXmlElement(): void
    {
        $element = new Element(
            content: 'a34d23b2-0cc0-4851-88fe-e3d9b74f4c31',
            attributes: ['isPermaLink' => 'false']
        );

        $guid = EpisodeGuid::fromXmlElement($element);

        self::assertEqualsCanonicalizing([
            'content' => 'a34d23b2-0cc0-4851-88fe-e3d9b74f4c31',
            'is_perma_link' => false,
        ], $guid->toArray());
    }

    public function testCreateFromInvalidXmlElement(): void
    {
        self::expectException(InvalidGuidElementException::class);

        EpisodeGuid::fromXmlElement(new Element());
    }
}
