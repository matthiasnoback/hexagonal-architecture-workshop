<?php

declare(strict_types=1);

namespace Billing\Handler;

use App\Session;
use Assert\Assert;
use Billing\UsageStatistics;
use Doctrine\DBAL\Connection;
use Laminas\Diactoros\Response\HtmlResponse;
use Laminas\Diactoros\Response\RedirectResponse;
use Mezzio\Router\RouterInterface;
use Mezzio\Template\TemplateRendererInterface;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

final class CreateInvoiceHandler implements RequestHandlerInterface
{
    public function __construct(
        private readonly Connection $connection,
        private readonly Session $session,
        private readonly RouterInterface $router,
        private readonly TemplateRendererInterface $renderer,
        private readonly ClientInterface $client,
        private readonly RequestFactoryInterface $requestFactory,
        private readonly UsageStatistics $usageStatistics
    ) {
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $formData = [
            'year' => date('Y'),
            'month' => date('m'),
            'organizerId' => $request->getAttribute('organizerId'),
        ];

        if ($request->getMethod() === 'POST') {
            $requestData = $request->getParsedBody();
            Assert::that($requestData)->isArray();

            $formData = array_merge($formData, $requestData);

            $year = $formData['year'];
            Assert::that($year)->integerish();
            $month = $formData['month'];
            Assert::that($month)->integerish();
            $organizerId = $formData['organizerId'];
            Assert::that($organizerId)->string();

            $numberOfMeetups = $this->usageStatistics->numberOfMeetupsOrganized($organizerId, (int)$year, (int)$month);

            // Alternative: use the API
            $response = $this->client->sendRequest(
                $this->requestFactory->createRequest(
                    'GET',
                    sprintf('/api/count-meetups/%s/%d/%d', $organizerId, $year, $month)
                )
            );
            $decodedData = json_decode($response->getBody()->getContents(), true);
            Assert::that($decodedData)->isArray();

            if ($numberOfMeetups > 0) {
                $invoiceAmount = $numberOfMeetups * 5;

                $this->connection->insert('invoices', [
                    'organizerId' => $organizerId,
                    'amount' => number_format($invoiceAmount, 2),
                    'year' => $year,
                    'month' => $month,
                ]);

                $this->session->addSuccessFlash('Invoice created');
            } else {
                $this->session->addErrorFlash('No need to create an invoice');
            }

            return new RedirectResponse($this->router->generateUri('list_organizers', [
                'id' => $organizerId,
            ]));
        }

        return new HtmlResponse($this->renderer->render('billing::create-invoice.html.twig', [
            'formData' => $formData,
            'years' => range(date('Y') - 1, date('Y')),
            'months' => range(1, 12),
        ]));
    }
}
