<?php

declare(strict_types=1);

namespace App\Handler;

use Assert\Assert;
use Doctrine\DBAL\Connection;
use Laminas\Diactoros\Response\HtmlResponse;
use Mezzio\Template\TemplateRendererInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

final class ListInvoicesHandler implements RequestHandlerInterface
{
    public function __construct(
        private readonly Connection $connection,
        private readonly TemplateRendererInterface $renderer
    ) {
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $organizerId = $request->getAttribute('organizerId');
        Assert::that($organizerId)->string();

        $invoices = $this->connection->fetchAllAssociative(
            'SELECT * FROM invoices WHERE organizerId = ?',
            [$organizerId]
        );

        return new HtmlResponse($this->renderer->render('admin::list-invoices.html.twig', [
            'invoices' => $invoices,
        ]));
    }
}
