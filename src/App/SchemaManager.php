<?php

declare(strict_types=1);

namespace App;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\DBAL\Schema\Synchronizer\SingleDatabaseSynchronizer;

final class SchemaManager
{
    public function __construct(
        private readonly Connection $connection
    ) {
    }

    public function updateSchema(): void
    {
        $synchronizer = new SingleDatabaseSynchronizer($this->connection);
        $synchronizer->updateSchema($this->provideSchema(), true);
    }

    private function provideSchema(): Schema
    {
        $schema = new Schema();

        $accountsTable = $schema->createTable('users');
        $accountsTable->addColumn('userId', 'integer', [
            'autoincrement' => true,
        ]);
        $accountsTable->addColumn('name', 'string');
        $accountsTable->addColumn('emailAddress', 'string');
        $accountsTable->addColumn('userType', 'string');
        $accountsTable->setPrimaryKey(['userId']);
        $accountsTable->addUniqueIndex(['emailAddress']);

        $meetupsTable = $schema->createTable('meetups');
        $meetupsTable->addColumn('meetupId', 'integer', [
            'autoincrement' => true,
        ]);
        $meetupsTable->addColumn('organizerId', 'integer');
        $meetupsTable->addColumn('name', 'string');
        $meetupsTable->addColumn('description', 'string');
        $meetupsTable->addColumn('scheduledFor', 'string');
        $meetupsTable->addColumn('wasCancelled', 'integer', [
            'default' => 0,
        ]);
        $meetupsTable->setPrimaryKey(['meetupId']);

        $rsvpsTable = $schema->createTable('rsvps');
        $rsvpsTable->addColumn('rsvpId', 'string');
        $rsvpsTable->addColumn('meetupId', 'string');
        $rsvpsTable->addColumn('userId', 'integer');
        $rsvpsTable->setPrimaryKey(['rsvpId']);
        $rsvpsTable->addUniqueIndex(['meetupId', 'userId']);

        return $schema;
    }
}
