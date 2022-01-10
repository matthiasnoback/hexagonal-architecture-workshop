<?php
declare(strict_types=1);

namespace App\Handler;

use Assert\Assert;
use App\Entity\UserId;
use Psr\Http\Server\RequestHandlerInterface;
use RuntimeException;
use App\Entity\UserRepository;
use App\Session;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Laminas\Diactoros\Response\RedirectResponse;

final class SwitchUserHandler implements RequestHandlerInterface
{
    private UserRepository $userRepository;

    private Session $session;

    public function __construct(
        UserRepository $userRepository,
        Session $session
    ) {
        $this->session = $session;
        $this->userRepository = $userRepository;
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $postData = $request->getParsedBody();
        Assert::that($postData)->isArray();

        if (!isset($postData['userId'])) {
            throw new RuntimeException('Bad request');
        }

        $user = $this->userRepository->getById(
            UserId::fromInt((int)$postData['userId'])
        );
        $this->session->setLoggedInUserId($user->userId());

        return new RedirectResponse('/');
    }
}
