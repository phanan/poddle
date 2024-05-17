<?php

namespace Tests\Values;

use PhanAn\Poddle\Exceptions\InvalidClosureElementException;
use PhanAn\Poddle\Values\Enclosure;
use Saloon\XmlWrangler\Data\Element;
use Tests\TestCase;

class EnclosureTest extends TestCase
{
    public function testCreateFromXmlElement(): void
    {
        $element = new Element(attributes: [
            'url' => 'https://example.com/audio.mp3',
            'type' => 'audio/mpeg',
            'length' => '123456',
        ]);

        $enclosure = Enclosure::fromXmlElement($element);

        self::assertEqualsCanonicalizing([
            'url' => 'https://example.com/audio.mp3',
            'type' => 'audio/mpeg',
            'length' => 123456,
        ], $enclosure->toArray());
    }

    public function testCreateFromInvalidXmlElement(): void
    {
        self::expectException(InvalidClosureElementException::class);

        Enclosure::fromXmlElement(new Element());
    }
}
