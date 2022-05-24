<?php

declare(strict_types=1);

namespace MeetupOrganizing\Handler;

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
        private readonly Connection $connection,
        private readonly TemplateRendererInterface $renderer
    ) {
    }

    public function handle(Request $request): ResponseInterface
    {
        $now = new DateTimeImmutable($_SERVER['HTTP_X-CURRENT-TIME'] ?? 'now');

        $showPastMeetups = ($request->getQueryParams()['showPastMeetups'] ?? 'no') === 'yes';

        $query = 'SELECT m.* FROM meetups m WHERE m.wasCancelled = 0';
        $parameters = [];

        if (!$showPastMeetups) {
            $query .= ' AND scheduledFor >= ?';
            $parameters[] = $now->format('Y-m-d H:i');
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
