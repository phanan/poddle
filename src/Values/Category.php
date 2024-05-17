<?php

namespace PhanAn\Poddle\Values;

use Illuminate\Support\Arr;
use PhanAn\Poddle\Exceptions\InvalidCategoryElementException;
use Saloon\XmlWrangler\Data\Element;
use Throwable;

class Category extends Serializable
{
    final public function __construct(public readonly string $text, public readonly ?Category $subCategory = null)
    {
    }

    public static function fromXmlElement(Element $element): static
    {
        try {
            return new static(
                text: $element->getAttribute('text'),
                subCategory: self::getSubCategoryFromXmlElement($element)
            );
        } catch (Throwable $exception) {
            throw new InvalidCategoryElementException($exception);
        }
    }

    public static function fromArray(array $data): static
    {
        return new static(
            text: Arr::get($data, 'text'),
            subCategory: optional(Arr::get($data, 'sub_category'), static fn ($sub) => static::fromArray($sub))
        );
    }

    /**
     * Get the subcategory from the given XML element.
     * NOTE: Following Apple's guide (https://help.apple.com/itc/podcasts_connect/#/itcb54353390),
     * we only take the first subcategory if there are multiple.
     */
    private static function getSubCategoryFromXmlElement(Element $element): ?Category
    {
        /** @var Element|null $subElement */
        $subElement = Arr::get($element->getContent(), 'itunes:category');

        if (!$subElement) {
            return null;
        }

        /** @var string|array<Element> $content */
        $content = $subElement->getContent();

        return $content
            ? new static($content[0]->getAttribute('text'))
            : new static($subElement->getAttribute('text'));
    }

    /** @return array<string, mixed> */
    public function toArray(): array
    {
        return [
            'text' => $this->text,
            'sub_category' => $this->subCategory?->toArray(),
        ];
    }
}
