<?php

declare(strict_types=1);

namespace App\Twig;

use App\Session;
use Twig\Extension\AbstractExtension;
use Twig\Extension\GlobalsInterface;

final class FlashExtension extends AbstractExtension implements GlobalsInterface
{
    public function __construct(
        private readonly Session $session
    ) {
    }

    public function getGlobals(): array
    {
        return [
            'allFlashes' => $this->session->getFlashes(),
        ];
    }
}
