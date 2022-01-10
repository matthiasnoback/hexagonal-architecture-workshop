<?php

declare(strict_types=1);

namespace App\Twig;

use App\Entity\UserRepository;
use App\Session;
use Twig\Extension\AbstractExtension;
use Twig\Extension\GlobalsInterface;

final class UserExtension extends AbstractExtension implements GlobalsInterface
{
    public function __construct(
        private readonly Session $session,
        private readonly UserRepository $userRepository
    ) {
    }

    public function getGlobals(): array
    {
        return [
            'loggedInUser' => $this->session->getLoggedInUser(),
            'allUsers' => $this->userRepository->findAll(),
        ];
    }
}
