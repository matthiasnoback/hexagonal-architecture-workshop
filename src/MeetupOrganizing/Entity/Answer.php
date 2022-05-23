<?php

declare(strict_types=1);

namespace MeetupOrganizing\Entity;

enum Answer: string
{
    case Yes = 'Yes';
    case No = 'No';
    case Unknown = 'Unknown';
}
