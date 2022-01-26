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

    protected function setUp(): void
    {
        /** @var ContainerInterface $container */
        $container = require 'config/container.php';

        /** @var SchemaManager $schemaManager */
        $schemaManager = $container->get(SchemaManager::class);
        $schemaManager->updateSchema();
        $schemaManager->truncateTables();

        $this->application = $container->get(ApplicationInterface::class);
    }
}
