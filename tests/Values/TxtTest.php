<?php

namespace Tests\Values;

use PhanAn\Poddle\Exceptions\InvalidTxtElementException;
use PhanAn\Poddle\Values\Txt;
use Saloon\XmlWrangler\Data\Element;
use Tests\TestCase;

class TxtTest extends TestCase
{
    public function testCreateFromXmlElement(): void
    {
        $txt = Txt::fromXmlElement(new Element(content: 'Hello, world!'));
        self::assertEqualsCanonicalizing(['content' => 'Hello, world!', 'purpose' => null], $txt->toArray());

        $txt = Txt::fromXmlElement(new Element(content: 'Hello, world!', attributes: ['purpose' => 'greeting']));
        self::assertEqualsCanonicalizing(['content' => 'Hello, world!', 'purpose' => 'greeting'], $txt->toArray());
    }

    public function testCreateFromInvalidXmlElement(): void
    {
        $this->expectException(InvalidTxtElementException::class);

        Txt::fromXmlElement(new Element());
    }
}
