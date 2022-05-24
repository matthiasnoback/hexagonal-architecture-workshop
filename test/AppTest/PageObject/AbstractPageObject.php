<?php

declare(strict_types=1);

namespace AppTest\PageObject;

use AppTest\SuccessfulResponse;
use AppTest\UnsuccessfulResponse;
use DOMNode;
use PHPUnit\Framework\Assert;
use Symfony\Component\BrowserKit\HttpBrowser;
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
            fn(DOMNode $node) => new static(new Crawler($node, $filter->getUri())),
            iterator_to_array($filter)
        );
    }

    protected static function assertSuccessfulResponse(HttpBrowser $browser): void
    {
        Assert::assertThat($browser->getInternalResponse(), new SuccessfulResponse());
    }

    protected static function assertUnsuccessfulResponse(HttpBrowser $browser): void
    {
        Assert::assertThat($browser->getInternalResponse(), new UnsuccessfulResponse());
    }
}
