<?php

namespace MeetupOrganizing\Entity;

final class CanNotRescheduleMeetup extends \RuntimeException
{
    public static function because(): self
    {
        return new self();
    }

    public static function becauseCurrentUserIsNotTheOrganizer(): self
    {
        return new self();
    }

    public static function becauseItWasCancelled(): self
    {
        return new self();
    }

    public static function becauseItAlreadyTookPlace(): self
    {
        return new self();
    }
}
