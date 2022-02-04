<?php

declare(strict_types=1);

namespace AppTest;

use App\ApplicationInterface;
use App\SchemaManager;
use Billing\Meetups;
use Billing\MeetupsForTesting;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;

abstract class AbstractApplicationTest extends TestCase
{
    protected ApplicationInterface $application;
    protected MeetupsForTesting $meetups;

    protected function setUp(): void
    {
        $_ENV['APPLICATION_ENV'] = 'application_testing';

        /** @var ContainerInterface $container */
        $container = require 'config/container.php';

        /** @var SchemaManager $schemaManager */
        $schemaManager = $container->get(SchemaManager::class);
        $schemaManager->updateSchema();
        $schemaManager->truncateTables();

        $this->application = $container->get(ApplicationInterface::class);

        $meetups = $container->get(Meetups::class);
        self::assertInstanceOf(MeetupsForTesting::class, $meetups);
        $this->meetups = $meetups;
    }
}
