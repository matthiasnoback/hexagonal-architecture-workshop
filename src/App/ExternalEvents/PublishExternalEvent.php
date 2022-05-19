<?php

declare(strict_types=1);

namespace App\ExternalEvents;

use App\Entity\UserHasSignedUp;

final class PublishExternalEvent
{
    public function __construct(
        private readonly ExternalEventPublisher $publisher
    ) {
    }

    public function whenUserHasSignedUp(UserHasSignedUp $event): void
    {
        $this->publisher->publish(
            'user.signed_up',
            [
                'id' => $event->userId()
                    ->asString(),
                'name' => $event->name(),
                'type' => $event->userType()
                    ->value,
            ]
        );
    }
}
