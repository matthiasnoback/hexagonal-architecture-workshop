<?php

declare(strict_types=1);

namespace AppTest\PageObject;

use DOMNode;
use Symfony\Component\DomCrawler\Crawler;

abstract class AbstractPageObject
{
    final public function __construct(
        protected Crawler $crawler
    ) {
    }

    /**
     * @return array<static>
     */
    public static function createManyFromCrawler(Crawler $filter): array
    {
        if (count($filter) === 0) {
            return [];
        }

        return array_map(
            fn (DOMNode $node) => new static(new Crawler($node, $filter->getUri())),
            iterator_to_array($filter)
        );
    }
}
