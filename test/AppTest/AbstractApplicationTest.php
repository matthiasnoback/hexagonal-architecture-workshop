<?php

declare(strict_types=1);

namespace AppTest;

use App\ApplicationInterface;
use App\SchemaManager;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;

abstract class AbstractApplicationTest extends TestCase
{
    protected ApplicationInterface $application;
    protected ContainerInterface $container;

    protected function setUp(): void
    {
        $_ENV['APPLICATION_ENV'] = 'application_testing';

        $this->container = require 'config/container.php';

        /** @var SchemaManager $schemaManager */
        $schemaManager = $this->container->get(SchemaManager::class);
        $schemaManager->updateSchema();
        $schemaManager->truncateTables();

        $this->application = $this->container->get(ApplicationInterface::class);
    }
}
