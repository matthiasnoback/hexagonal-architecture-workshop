<?php

declare(strict_types=1);

namespace Billing\Handler;

use App\Mapping;
use Billing\ViewModel\Organizer;
use Doctrine\DBAL\Connection;
use Laminas\Diactoros\Response\HtmlResponse;
use Mezzio\Template\TemplateRendererInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

final class ListOrganizersHandler implements RequestHandlerInterface
{
    public function __construct(
        private readonly Connection $connection,
        private readonly TemplateRendererInterface $renderer
    ) {
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $organizers = array_map(
            fn(array $record) => new Organizer(
                Mapping::getString($record, 'organizerId'),
                Mapping::getString($record, 'name'),
            ),
            $this->connection->fetchAllAssociative(
                'SELECT * FROM billing_organizers'
            )
        );

        return new HtmlResponse(
            $this->renderer->render('admin::list-organizers.html.twig', [
                'organizers' => $organizers,
            ])
        );
    }
}
