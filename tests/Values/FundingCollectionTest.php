<?php

namespace Tests\Values;

use PhanAn\Poddle\Values\FundingCollection;
use Saloon\XmlWrangler\Data\Element;
use Tests\TestCase;

class FundingCollectionTest extends TestCase
{
    public function testCreateFromXmlElements(): void
    {
        $collection = FundingCollection::fromXmlElements(collect([
            new Element('Buy me a coffee!', ['url' => 'https://buymea.coffee']),
            new Element('Buy me a beer!', ['url' => 'https://buymea.beer'])
        ]));

        self::assertEqualsCanonicalizing([
            ['text' => 'Buy me a coffee!', 'url' => 'https://buymea.coffee'],
            ['text' => 'Buy me a beer!', 'url' => 'https://buymea.beer'],
        ], $collection->toArray());
    }
}
