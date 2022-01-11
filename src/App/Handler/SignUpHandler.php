<?php

declare(strict_types=1);

namespace App\Handler;

use App\Entity\UserType;
use App\Session;
use Assert\Assert;
use Doctrine\DBAL\Connection;
use Laminas\Diactoros\Response\HtmlResponse;
use Laminas\Diactoros\Response\RedirectResponse;
use Mezzio\Router\RouterInterface;
use Mezzio\Template\TemplateRendererInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

final class SignUpHandler implements RequestHandlerInterface
{
    public function __construct(
        private readonly TemplateRendererInterface $renderer,
        private readonly Connection $connection,
        private readonly RouterInterface $router,
        private readonly Session $session
    ) {
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        if ($this->session->isUserLoggedIn()) {
            return new RedirectResponse('/');
        }

        $formData = [
            'name' => '',
            'userType' => UserType::RegularUser->name,
            'emailAddress' => '',
        ];
        $formErrors = [];

        if ($request->getMethod() === 'POST') {
            $requestData = $request->getParsedBody();
            Assert::that($requestData)->isArray();

            $formData = array_merge($formData, $requestData);

            if ($formData['name'] === '') {
                $formErrors['name'][] = 'Please provide a name';
            }
            if ($formData['emailAddress'] === '') {
                $formErrors['emailAddress'][] = 'Please provide an email address';
            }

            if ($formErrors === []) {
                $this->connection->insert(
                    'users',
                    [
                        'name' => $formData['name'],
                        'emailAddress' => $formData['emailAddress'],
                        'userType' => UserType::from($formData['userType'])->name,
                    ]
                );

                $this->session->addSuccessFlash('You have been registered as a user');

                return new RedirectResponse($this->router->generateUri('login'));
            }
        }

        return new HtmlResponse(
            $this->renderer->render('app::sign-up.html.twig', [
                'formData' => $formData,
                'formErrors' => $formErrors,
                'userTypes' => UserType::cases(),
            ])
        );
    }
}
