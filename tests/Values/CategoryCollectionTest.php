<?php

namespace Tests\Values;

use PhanAn\Poddle\Values\CategoryCollection;
use Saloon\XmlWrangler\Data\Element;
use Tests\TestCase;

class CategoryCollectionTest extends TestCase
{
    public function testCreateFromXmlElements(): void
    {
        $collection = CategoryCollection::fromXmlElements(collect([
            new Element(attributes: ['text' => 'News']),
            new Element(attributes: ['text' => 'Entertainment']),
        ]));

        self::assertEqualsCanonicalizing([
            ['text' => 'News', 'sub_category' => null],
            ['text' => 'Entertainment', 'sub_category' => null],
        ], $collection->toArray());
    }
}
