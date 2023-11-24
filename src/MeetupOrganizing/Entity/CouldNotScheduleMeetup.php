<?php

namespace MeetupOrganizing\Entity;

class CouldNotScheduleMeetup extends \InvalidArgumentException
{
    public static function becauseTheDateIsInThePast(): self
    {
        return new self();
    }
}
