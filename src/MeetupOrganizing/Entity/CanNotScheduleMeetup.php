<?php

namespace MeetupOrganizing\Entity;

class CanNotScheduleMeetup extends \RuntimeException
{
    public static function becauseUserIsNotAnOrganizer(): self
    {
        return new self();
    }

    public static function becauseTheDateIsInThePast(): self
    {
        return new self();
    }
}
