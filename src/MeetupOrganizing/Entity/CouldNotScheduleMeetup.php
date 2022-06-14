<?php
declare(strict_types=1);

namespace MeetupOrganizing\Entity;

final class CouldNotScheduleMeetup extends \RuntimeException
{
    public string $formError;

    public static function becauseTheDateIsInThePast(): self
    {
        $exception = new self(
            '...',
        );
        $exception->formError = 'The date is in the past';

        return $exception;
    }
}
