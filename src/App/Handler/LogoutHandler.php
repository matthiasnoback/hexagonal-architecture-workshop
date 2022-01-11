<?php

declare(strict_types=1);

namespace App\Handler;

use App\Session;
use Laminas\Diactoros\Response\RedirectResponse;
use Mezzio\Router\RouterInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

final class LogoutHandler implements RequestHandlerInterface
{
    public function __construct(
        private readonly Session $session,
        private readonly RouterInterface $router
    ) {
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $this->session->logout();

        $this->session->addSuccessFlash('You are now logged out');

        return new RedirectResponse($this->router->generateUri('list_meetups'));
    }
}
