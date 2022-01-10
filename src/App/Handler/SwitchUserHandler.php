<?php

declare(strict_types=1);

namespace App\Handler;

use App\Entity\UserId;
use App\Entity\UserRepository;
use App\Session;
use Assert\Assert;
use Laminas\Diactoros\Response\RedirectResponse;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use RuntimeException;

final class SwitchUserHandler implements RequestHandlerInterface
{
    public function __construct(
        private readonly UserRepository $userRepository,
        private readonly Session $session
    ) {
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $postData = $request->getParsedBody();
        Assert::that($postData)->isArray();

        if (! isset($postData['userId'])) {
            throw new RuntimeException('Bad request');
        }

        $user = $this->userRepository->getById(UserId::fromInt((int) $postData['userId']));
        $this->session->setLoggedInUserId($user->userId());

        return new RedirectResponse('/');
    }
}
