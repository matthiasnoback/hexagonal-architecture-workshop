<?php

namespace MeetupOrganizing\Entity;

class CouldNotRescheduleMeetup extends \RuntimeException
{
    public static function becauseTheMeetupWasCancelled(): self
    {
        return new self();
    }

    public static function becauseTheMeetupAlreadyTookPlace(): self
    {
        return new self();
    }
}
