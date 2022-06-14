<?php

declare(strict_types=1);

namespace MeetupOrganizing\Handler;

use App\Clock;
use Doctrine\DBAL\Connection;
use Laminas\Diactoros\Response\HtmlResponse;
use Mezzio\Template\TemplateRendererInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface;

final class ListMeetupsHandler implements RequestHandlerInterface
{
    public function __construct(
        private readonly Connection $connection,
        private readonly TemplateRendererInterface $renderer,
        private readonly Clock $clock,
    ) {
    }

    public function handle(Request $request): ResponseInterface
    {
        $showPastMeetups = ($request->getQueryParams()['showPastMeetups'] ?? 'no') === 'yes';

        $query = 'SELECT m.* FROM meetups m WHERE m.wasCancelled = 0';
        $parameters = [];

        if (!$showPastMeetups) {
            $query .= ' AND scheduledFor >= ?';
            $parameters[] = $this->clock->now()->format('Y-m-d H:i');
        }

        $meetups = $this->connection->fetchAllAssociative($query, $parameters);

        return new HtmlResponse(
            $this->renderer->render('app::list-meetups.html.twig', [
                'meetups' => $meetups,
                'showPastMeetups' => $showPastMeetups,
            ])
        );
    }
}
