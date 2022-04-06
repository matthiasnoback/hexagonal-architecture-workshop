<?php
declare(strict_types=1);

namespace Billing\Projections;

use App\ExternalEvents\ExternalEventConsumer;
use App\Mapping;
use Doctrine\DBAL\Connection;

final class OrganizerProjection implements ExternalEventConsumer
{
    public function __construct(private readonly Connection $connection)
    {
    }

    public function whenConsumerRestarted(): void
    {
        $this->connection->executeQuery('DELETE FROM billing_organizers WHERE 1');
    }

    public function whenExternalEventReceived(
        string $eventType,
        array $eventData,
    ): void {
        if ($eventType !== 'user.signed_up') {
            return;
        }

        if (Mapping::getString($eventData, 'type') !== 'Organizer') {
            // Only process organizers
            return;
        }

        // This is a new organizer
        $this->connection->insert('billing_organizers', [
            'organizerId' => Mapping::getString($eventData, 'id'),
            'name' => Mapping::getString($eventData, 'name')
        ]);
    }
}
