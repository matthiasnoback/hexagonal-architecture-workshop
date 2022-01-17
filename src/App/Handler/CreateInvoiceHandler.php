<?php

declare(strict_types=1);

namespace App\Handler;

use App\Session;
use Assert\Assert;
use DateTimeImmutable;
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
        private readonly RequestFactoryInterface $requestFactory
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

            $firstDayOfMonth = DateTimeImmutable::createFromFormat('Y-m-d', $year . '-' . $month . '-1');
            Assert::that($firstDayOfMonth)->isInstanceOf(DateTimeImmutable::class);
            $lastDayOfMonth = $firstDayOfMonth->modify('last day of this month');

            // Load the data directly from the database
            $result = $this->connection->executeQuery(
                'SELECT COUNT(meetupId) as numberOfMeetups FROM meetups WHERE organizerId = :organizerId AND scheduledFor >= :firstDayOfMonth AND scheduledFor <= :lastDayOfMonth',
                [
                    'organizerId' => $organizerId,
                    'firstDayOfMonth' => $firstDayOfMonth->format('Y-m-d'),
                    'lastDayOfMonth' => $lastDayOfMonth->format('Y-m-d'),
                ]
            );

            // Alternative: use the API
            $response = $this->client->sendRequest(
                $this->requestFactory->createRequest(
                    'GET',
                    sprintf('http://api:8080/api/count-meetups/%s/%d/%d', $organizerId, $year, $month)
                )
            );

            $record = $result->fetchAssociative();
            Assert::that($record)->isArray();
            $numberOfMeetups = $record['numberOfMeetups'];
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

        return new HtmlResponse($this->renderer->render('admin::create-invoice.html.twig', [
            'formData' => $formData,
            'years' => range(date('Y') - 1, date('Y')),
            'months' => range(1, 12),
        ]));
    }
}
