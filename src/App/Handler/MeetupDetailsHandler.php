<?php

declare(strict_types=1);

namespace App\Handler;

use Laminas\Diactoros\Response\HtmlResponse;
use Mezzio\Template\TemplateRendererInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

final class MeetupDetailsHandler implements RequestHandlerInterface
{
    public function __construct(
        private readonly MeetupDetailsRepository $meetupDetailsRepository,
        private readonly TemplateRendererInterface $renderer
    ) {
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $meetupDetails = $this->meetupDetailsRepository->getById($request->getAttribute('id'));

        return new HtmlResponse(
            $this->renderer->render('app::meetup-details.html.twig', [
                'meetupDetails' => $meetupDetails,
            ])
        );
    }
}
