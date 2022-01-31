<?php
declare(strict_types=1);

namespace AppTest;

use App\ApplicationInterface;
use App\SchemaManager;
use AppTest\Billing\MeetupRepositoryStub;
use Billing\MeetupRepositoryInterface;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;

abstract class AbstractApplicationTest extends TestCase
{
    protected ApplicationInterface $application;
    protected ContainerInterface $container;
    protected MeetupRepositoryStub $meetupRepository;

    protected function setUp(): void
    {
        $_ENV['APPLICATION_ENV'] = 'application_testing';

        $this->container = require 'config/container.php';

        $this->meetupRepository = $this->container->get(MeetupRepositoryInterface::class);

        /** @var SchemaManager $schemaManager */
        $schemaManager = $this->container->get(SchemaManager::class);
        $schemaManager->updateSchema();
        $schemaManager->truncateTables();

        $this->application = $this->container->get(ApplicationInterface::class);
    }
}
