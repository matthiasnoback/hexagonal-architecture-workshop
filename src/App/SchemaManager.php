<?php

declare(strict_types=1);

namespace App;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Schema\Comparator;
use Doctrine\DBAL\Schema\Schema;

final class SchemaManager
{
    public function __construct(
        private readonly Connection $connection
    ) {
    }

    public function updateSchema(): void
    {
        $schemaDiff = (new Comparator())->compare(
            $this->connection->getSchemaManager()->createSchema(),
            $this->provideSchema()
        );

        foreach ($schemaDiff->toSaveSql($this->connection->getDatabasePlatform()) as $sql) {
            $this->connection->executeStatement($sql);
        }
    }

    public function truncateTables(): void
    {
        foreach ($this->provideSchema()->getTables() as $table) {
            $this->connection->executeQuery(
                $this->connection->getDatabasePlatform()
                    ->getTruncateTableSQL($table->getName())
            );
        }
    }

    private function provideSchema(): Schema
    {
        $schema = new Schema();

        $accountsTable = $schema->createTable('users');
        $accountsTable->addColumn('userId', 'string');
        $accountsTable->addColumn('name', 'string');
        $accountsTable->addColumn('emailAddress', 'string');
        $accountsTable->addColumn('userType', 'string');
        $accountsTable->setPrimaryKey(['userId']);
        $accountsTable->addUniqueIndex(['emailAddress']);

        $meetupsTable = $schema->createTable('meetups');
        $meetupsTable->addColumn('meetupId', 'integer', [
            'autoincrement' => true,
        ]);
        $meetupsTable->addColumn('organizerId', 'string');
        $meetupsTable->addColumn('name', 'string');
        $meetupsTable->addColumn('description', 'string');
        $meetupsTable->addColumn('scheduledFor', 'string');
        $meetupsTable->addColumn('wasCancelled', 'integer', [
            'default' => 0,
        ]);
        $meetupsTable->setPrimaryKey(['meetupId']);

        $invoicesTable = $schema->createTable('invoices');
        $invoicesTable->addColumn('invoiceId', 'integer', [
            'autoincrement' => true,
        ]);
        $invoicesTable->addColumn('organizerId', 'string');
        $invoicesTable->addColumn('amount', 'string');
        $invoicesTable->addColumn('year', 'integer');
        $invoicesTable->addColumn('month', 'integer');
        $invoicesTable->setPrimaryKey(['invoiceId']);
        $invoicesTable->addUniqueIndex(['organizerId', 'year', 'month']);

        $rsvpsTable = $schema->createTable('rsvps');
        $rsvpsTable->addColumn('rsvpId', 'string');
        $rsvpsTable->addColumn('meetupId', 'string');
        $rsvpsTable->addColumn('userId', 'string');
        $rsvpsTable->setPrimaryKey(['rsvpId']);
        $rsvpsTable->addUniqueIndex(['meetupId', 'userId']);

        $billingOrganizersTable = $schema->createTable('billing_organizers');
        $billingOrganizersTable->addColumn('organizerId', 'string');
        $billingOrganizersTable->addColumn('name', 'string');
        $billingOrganizersTable->setPrimaryKey(['organizerId']);

        $billingMeetupsTable = $schema->createTable('billing_meetups');
        $billingMeetupsTable->addColumn('organizerId', 'string');
        $billingMeetupsTable->addColumn('meetupId', 'string');
        $billingMeetupsTable->addColumn('year', 'integer');
        $billingMeetupsTable->addColumn('month', 'integer');
        $billingMeetupsTable->setPrimaryKey(['meetupId']);

        $outboxTable = $schema->createTable('outbox');
        $outboxTable->addColumn('messageId', 'integer', [
            'autoincrement' => true,
        ]);
        $outboxTable->addColumn('messageType', 'string');
        $outboxTable->addColumn('messageData', 'string');
        $outboxTable->addColumn('wasPublished', 'integer', ['default' => 0]);
        $outboxTable->setPrimaryKey(['messageId']);

        return $schema;
    }
}
