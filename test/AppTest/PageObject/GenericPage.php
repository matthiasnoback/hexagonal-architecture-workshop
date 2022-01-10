<?php

declare(strict_types=1);

namespace AppTest\PageObject;

final class GenericPage extends AbstractPageObject
{
    /**
     * @return array<string>
     */
    public function getFlashMessages(): array
    {
        $nodes = $this->crawler->filter('.flash-message');
        if (count($nodes) === 0) {
            return [];
        }

        return array_map(fn (\DOMNode $node) => trim($node->textContent), iterator_to_array($nodes));
    }
}
