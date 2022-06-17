<?php

declare(strict_types=1);

namespace MeetupOrganizing\Handler;

use App\ApplicationInterface;
use Laminas\Diactoros\Response\HtmlResponse;
use Mezzio\Template\TemplateRendererInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface;

final class ListMeetupsHandler implements RequestHandlerInterface
{
    public function __construct(
        private readonly TemplateRendererInterface $renderer,
        private readonly ApplicationInterface $application,
    ) {
    }

    public function handle(Request $request): ResponseInterface
    {
        $showPastMeetups = ($request->getQueryParams()['showPastMeetups'] ?? 'no') === 'yes';

        return new HtmlResponse(
            $this->renderer->render('app::list-meetups.html.twig', [
                'meetups' => $this->application->listMeetups($showPastMeetups),
                'showPastMeetups' => $showPastMeetups,
            ])
        );
    }
}
