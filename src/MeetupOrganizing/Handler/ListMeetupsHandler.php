<?php

declare(strict_types=1);

namespace MeetupOrganizing\Handler;

use App\ApplicationInterface;
use App\ListUpcomingMeetups;
use DateTimeImmutable;
use Laminas\Diactoros\Response\HtmlResponse;
use MeetupOrganizing\Entity\ScheduledDate;
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
        $now = new DateTimeImmutable();

        $upcomingMeetups = $this->application->listUpcomingMeetups(
            new ListUpcomingMeetups(
                $now->format(ScheduledDate::DATE_TIME_FORMAT)
            )
        );

        return new HtmlResponse(
            $this->renderer->render('app::list-meetups.html.twig', [
                'upcomingMeetups' => $upcomingMeetups,
            ])
        );
    }
}
