<?php
declare(strict_types=1);

namespace MeetupOrganizing\Entity;

use RuntimeException;

final class CouldNotScheduleMeetup extends RuntimeException
{
    public string $userMessage;

    public function __construct(string $message, string $userMessage)
    {
        parent::__construct($message);

        $this->userMessage = $userMessage;
    }

    public static function becauseTheDateIsInThePast(): self
    {
        return new self(
            'Could not schedule the meetup because the date is in the past',
            'A meetup can only be scheduled on a future date'
        );
    }
}
