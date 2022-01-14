<?php

declare(strict_types=1);

namespace App\Handler;

use Assert\Assert;
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
        $meetupId = $request->getAttribute('id');
        Assert::that($meetupId)->string();

        $meetupDetails = $this->meetupDetailsRepository->getById($meetupId);

        return new HtmlResponse(
            $this->renderer->render('app::meetup-details.html.twig', [
                'meetupDetails' => $meetupDetails,
            ])
        );
    }
}
