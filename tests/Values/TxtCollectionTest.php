<?php

namespace Tests\Values;

use PhanAn\Poddle\Values\TxtCollection;
use Saloon\XmlWrangler\Data\Element;
use Tests\TestCase;

class TxtCollectionTest extends TestCase
{
    public function testCreateFromXmlElements(): void
    {
        $txtCollection = TxtCollection::fromXmlElements(collect([
            new Element(content: 'Hello, world!'),
            new Element(content: 'Goodbye, world!', attributes: ['purpose' => 'verify']),
        ]));

        self::assertEqualsCanonicalizing([
            ['content' => 'Hello, world!', 'purpose' => null],
            ['content' => 'Goodbye, world!', 'purpose' => 'verify'],
        ], $txtCollection->toArray());
    }
}
