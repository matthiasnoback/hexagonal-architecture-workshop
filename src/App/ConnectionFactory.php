<?php

declare(strict_types=1);

namespace App;

use Assert\Assert;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\DriverManager;
use Psr\Container\ContainerInterface;

final class ConnectionFactory
{
    public function __invoke(ContainerInterface $container): Connection
    {
        $config = $container->get('config');
        Assert::that($config)->isArray();

        $projectRootDir = $config['project_root_dir'];
        Assert::that($projectRootDir)->directory();

        $sqliteFile = $projectRootDir . '/var/app-' . ($config['environment'] ?? 'development') . '.sqlite';

        $connection = DriverManager::getConnection([
            'driver' => 'pdo_sqlite',
            'path' => $sqliteFile,
        ]);
        (new SchemaManager($connection))->updateSchema();

        return $connection;
    }
}
