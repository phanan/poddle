<?php

namespace Tests\Values;

use PhanAn\Poddle\Exceptions\InvalidCategoryElementException;
use PhanAn\Poddle\Values\Category;
use Saloon\XmlWrangler\Data\Element;
use Tests\TestCase;

class CategoryTest extends TestCase
{
    public function testCreateFromXmlElement(): void
    {
        $element = new Element(attributes: ['text' => 'News']);
        $category = Category::fromXmlElement($element);

        self::assertEqualsCanonicalizing([
            'text' => 'News',
            'sub_category' => null,
        ], $category->toArray());
    }

    public function testCreateWithSubcategory(): void
    {
        $element = new Element(
            content: ['itunes:category' => new Element(attributes: ['text' => 'Tech News'])],
            attributes: ['text' => 'News'],
        );

        $category = Category::fromXmlElement($element);

        self::assertEqualsCanonicalizing([
            'text' => 'News',
            'sub_category' => [
                'text' => 'Tech News',
                'sub_category' => null,
            ],
        ], $category->toArray());
    }

    public function testMultipleSubcategories(): void
    {
        $element = new Element(
            content: [
                'itunes:category' => new Element(content: [
                    new Element(attributes: ['text' => 'Gadgets']),
                    new Element(attributes: ['text' => 'Tech News']),
                ]),
            ],
            attributes: ['text' => 'News'],
        );

        $category = Category::fromXmlElement($element);

        self::assertEqualsCanonicalizing([
            'text' => 'News',
            'sub_category' => [
                'text' => 'Gadgets',
                'sub_category' => null,
            ],
        ], $category->toArray());
    }

    public function testCreateFromInvalidXmlElement(): void
    {
        self::expectException(InvalidCategoryElementException::class);

        Category::fromXmlElement(new Element());
    }
}
