<?php

namespace PhanAn\Poddle\Values;

use Illuminate\Support\Collection;
use Illuminate\Support\Enumerable;
use Saloon\XmlWrangler\Data\Element;

/**
 * @template TKey of array-key
 * @template TModel of Category
 * @extends Collection<TKey, TModel>
 */
class CategoryCollection extends Collection
{
    /**
     * @param  array<TModel>  $items
     */
    public function __construct(array $items = [])
    {
        parent::__construct($items);
    }

    public static function fromXmlElements(Enumerable $elements): static
    {
        return tap(new static(), static function (self $collection) use ($elements): void {
            $elements->each(static fn (Element $element) => $collection->add(Category::fromXmlElement($element)));
        });
    }

    /**
     * @param  array<array-key, array<array-key, mixed>>  $data
     */
    public static function fromArray(array $data): static
    {
        return tap(new static(), static function (self $collection) use ($data): void {
            foreach ($data as $item) {
                $collection->add(Category::fromArray($item));
            }
        });
    }
}
