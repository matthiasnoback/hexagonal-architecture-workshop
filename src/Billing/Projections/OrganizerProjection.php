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
        if ($event->messageType() !== 'user.signed_up') {
            return;
        }

        if (Mapping::getString($event->messageData(), 'type') !== 'Organizer') {
            // Only process organizers
            return;
        }

        $result = $this->connection->fetchAssociative('SELECT * FROM billing_organizers WHERE organizerId = ?', [
            Mapping::getString($event->messageData(), 'id')
        ]);

        if (is_array($result)) {
            // We already know this organizer
            return;
        }

        // This is a new organizer
        $this->connection->insert('billing_organizers', [
            'organizerId' => Mapping::getString($event->messageData(), 'id'),
            'name' => Mapping::getString($event->messageData(), 'name')
        ]);
    }
}
