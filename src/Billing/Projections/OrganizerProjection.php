<?php
declare(strict_types=1);

namespace Billing\Projections;

use App\ExternalEventReceived;
use App\Mapping;
use Doctrine\DBAL\Connection;

final class OrganizerProjection
{
    public function __construct(private readonly Connection $connection)
    {
    }

    public function whenConsumerRestarted(): void
    {
        $this->connection->executeQuery('DELETE FROM billing_organizers WHERE 1');
    }

    public function whenExternalEventReceived(ExternalEventReceived $event): void
    {
        if ($event->eventType() !== 'user.signed_up') {
            return;
        }

        if (Mapping::getString($event->eventData(), 'type') !== 'Organizer') {
            // Only process organizers
            return;
        }

        // This is a new organizer
        $this->connection->insert('billing_organizers', [
            'organizerId' => Mapping::getString($event->eventData(), 'id'),
            'name' => Mapping::getString($event->eventData(), 'name')
        ]);
    }
}
