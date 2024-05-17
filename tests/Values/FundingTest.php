<?php

namespace Tests\Values;

use PhanAn\Poddle\Exceptions\InvalidFundingElementException;
use PhanAn\Poddle\Values\Funding;
use Saloon\XmlWrangler\Data\Element;
use Tests\TestCase;

class FundingTest extends TestCase
{
    public function testCreateFromXmlElement(): void
    {
        $element = new Element(
            content: 'Buy me a coffee!',
            attributes: ['url' => 'https://buymea.coffee/phanan']
        );

        $enclosure = Funding::fromXmlElement($element);

        self::assertEqualsCanonicalizing([
            'url' => 'https://buymea.coffee/phanan',
            'text' => 'Buy me a coffee!',
        ], $enclosure->toArray());
    }

    public function testCreateFromInvalidXmlElement(): void
    {
        self::expectException(InvalidFundingElementException::class);

        Funding::fromXmlElement(new Element());
    }
}
