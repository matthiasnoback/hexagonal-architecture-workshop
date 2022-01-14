<?php

declare(strict_types=1);

namespace App\Handler;

use App\Entity\UserId;
use App\Entity\UserRepository;
use App\Session;
use Assert\Assert;
use Laminas\Diactoros\Response\RedirectResponse;
use Mezzio\Router\RouterInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

final class SwitchUserHandler implements RequestHandlerInterface
{
    public function __construct(
        private readonly UserRepository $userRepository,
        private readonly Session $session,
        private readonly RouterInterface $router
    ) {
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $requestData = $request->getParsedBody();
        Assert::that($requestData)->isArray();

        $userId = $requestData['userId'];
        if ($userId === '') {
            $this->session->logout();
        } else {
            $user = $this->userRepository->getById(UserId::fromString($userId));

            $this->session->setLoggedInUserId($user->userId());
        }

        return new RedirectResponse($this->router->generateUri('list_meetups'));
    }
}
