<?php

declare(strict_types=1);

namespace MeetupOrganizing\Handler;

use App\Entity\UserType;
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
        $organizers = $this->connection->fetchAllAssociative(
            'SELECT * FROM users WHERE userType = ?',
            [UserType::Organizer->name]
        );

        return new HtmlResponse(
            $this->renderer->render('admin::list-organizers.html.twig', [
                'organizers' => $organizers,
            ])
        );
    }
}
