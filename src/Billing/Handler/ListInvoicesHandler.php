<?php

declare(strict_types=1);

namespace Billing\Handler;

use App\ApplicationInterface;
use Assert\Assert;
use Laminas\Diactoros\Response\HtmlResponse;
use Mezzio\Template\TemplateRendererInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

final class ListInvoicesHandler implements RequestHandlerInterface
{
    public function __construct(
        private readonly ApplicationInterface $application,
        private readonly TemplateRendererInterface $renderer
    ) {
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $organizerId = $request->getAttribute('organizerId');
        Assert::that($organizerId)->string();

        return new HtmlResponse($this->renderer->render('billing::list-invoices.html.twig', [
            'invoices' => $this->application->listInvoices($organizerId),
            'organizerId' => $organizerId,
        ]));
    }
}
