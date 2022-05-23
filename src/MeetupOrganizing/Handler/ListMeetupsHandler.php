<?php

declare(strict_types=1);

namespace MeetupOrganizing\Handler;

use Assert\Assert;
use DateTimeImmutable;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Driver\Statement;
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
        $now = new DateTimeImmutable();

        $showPastMeetups = ($request->getQueryParams()['showPastMeetups'] ?? 'no') === 'yes';

        $queryBuilder = $this->connection->createQueryBuilder()
            ->select('*')
            ->from('meetups')
            ->andWhere('wasCancelled = :wasNotCancelled')
            ->setParameter('wasNotCancelled', 0);

        if (!$showPastMeetups) {
            $queryBuilder->andWhere('scheduledFor >= :now')
                ->setParameter('now', $now->format('Y-m-d H:i'));
        }

        $statement = $queryBuilder->execute();
        Assert::that($statement)->isInstanceOf(Statement::class);

        $upcomingMeetups = $statement->fetchAllAssociative();

        return new HtmlResponse(
            $this->renderer->render('app::list-meetups.html.twig', [
                'upcomingMeetups' => $upcomingMeetups,
                'showPastMeetups' => $showPastMeetups,
            ])
        );
    }
}
