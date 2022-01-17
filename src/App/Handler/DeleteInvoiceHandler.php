<?php

declare(strict_types=1);

namespace App\Handler;

use Assert\Assert;
use Doctrine\DBAL\Connection;
use Laminas\Diactoros\Response\RedirectResponse;
use Mezzio\Router\RouterInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

final class DeleteInvoiceHandler implements RequestHandlerInterface
{
    public function __construct(
        private readonly Connection $connection,
        private readonly RouterInterface $router
    ) {
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $invoiceId = $request->getAttribute('invoiceId');
        Assert::that($invoiceId)->string();

        $organizerId = $request->getAttribute('organizerId');
        Assert::that($organizerId)->string();

        $this->connection->delete('invoices', [
            'organizerId' => $organizerId,
            'invoiceId' => $invoiceId,
        ]);

        return new RedirectResponse($this->router->generateUri('list_invoices', [
            'organizerId' => $organizerId,
        ]));
    }
}
