<?php

declare(strict_types=1);

namespace MeetupOrganizing\Handler;

use App\ApplicationInterface;
use DateTimeImmutable;
use Doctrine\DBAL\Connection;
use Laminas\Diactoros\Response\HtmlResponse;
use Mezzio\Template\TemplateRendererInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface;

final class ListMeetupsHandler implements RequestHandlerInterface
{
    public function __construct(
        private readonly ApplicationInterface $application,
        private readonly TemplateRendererInterface $renderer
    ) {
    }

    public function handle(Request $request): ResponseInterface
    {
        $now = $_SERVER['HTTP_X_CURRENT_TIME'] ?? 'now';

        $showPastMeetups = ($request->getQueryParams()['showPastMeetups'] ?? 'no') === 'yes';

        $meetups = $this->application->listMeetups(
            $now,
            $showPastMeetups
        );

        return new HtmlResponse(
            $this->renderer->render('app::list-meetups.html.twig', [
                'meetups' => $meetups,
                'showPastMeetups' => $showPastMeetups,
            ])
        );
    }
}
