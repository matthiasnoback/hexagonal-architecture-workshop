<?php

declare(strict_types=1);

namespace App\Handler;

use App\Entity\CouldNotFindUser;
use App\Entity\UserRepository;
use App\Session;
use Assert\Assert;
use Laminas\Diactoros\Response\HtmlResponse;
use Laminas\Diactoros\Response\RedirectResponse;
use Mezzio\Template\TemplateRendererInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

final class LoginHandler implements RequestHandlerInterface
{
    public function __construct(
        private readonly UserRepository $userRepository,
        private readonly Session $session,
        private readonly TemplateRendererInterface $renderer
    ) {
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        if ($this->session->isUserLoggedIn()) {
            return new RedirectResponse('/');
        }

        $formData = [
            'emailAddress' => '',
        ];
        $formErrors = [];

        if ($request->getMethod() === 'POST') {
            $requestData = $request->getParsedBody();
            Assert::that($requestData)->isArray();

            $formData = array_merge($formData, $requestData);

            if ($formData['emailAddress'] === '') {
                $formErrors['emailAddress'][] = 'Please provide an email address';
            }

            try {
                $user = $this->userRepository->getByEmailAddress($formData['emailAddress']);

                $this->session->setLoggedInUserId($user->userId());

                $this->session->addSuccessFlash('You have successfully logged in');

                return new RedirectResponse('/');
            } catch (CouldNotFindUser) {
                $formErrors['emailAddress'][] = 'Unknown email address';
            }
        }

        return new HtmlResponse(
            $this->renderer->render(
                'app::login.html.twig',
                [
                    'formData' => $formData,
                    'formErrors' => $formErrors,
                ]
            )
        );
    }
}
