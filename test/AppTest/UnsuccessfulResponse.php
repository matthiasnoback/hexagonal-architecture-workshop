<?php

declare(strict_types=1);

namespace AppTest;

use Assert\Assertion;
use PHPUnit\Framework\Constraint\Constraint;
use Symfony\Component\BrowserKit\Response;

final class UnsuccessfulResponse extends Constraint
{
    public function toString(): string
    {
        return ' was an unsuccessful response';
    }

    protected function matches($other): bool
    {
        Assertion::isInstanceOf($other, Response::class);
        /** @var Response $other */

        if ($other->getStatusCode() < 400) {
            return false;
        }

        if ($other->getStatusCode() >= 500) {
            return false;
        }

        return true;
    }

    protected function failureDescription($other): string
    {
        Assertion::isInstanceOf($other, Response::class);
        /** @var Response $other */

        $content = $other->getContent();
        $endOfErrorMessage = strpos($content, '-->');
        if ($endOfErrorMessage === false) {
            $showContent = substr($content, 0, 500);
        } else {
            $showContent = substr($content, 0, $endOfErrorMessage);
        }

        return trim($showContent) . ' [...] ' . $this->toString();
    }
}
