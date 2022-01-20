<?php

declare(strict_types=1);

namespace App;

use MeetupOrganizing\Application\SignUp;
use MeetupOrganizing\ViewModel\MeetupDetails;

interface ApplicationInterface
{
    public function signUp(SignUp $command): void;

    public function meetupDetails(string $id): MeetupDetails;

    /**
     * @TODO try passing the ID as an argument
     */
    public function scheduleMeetup(
        string $name,
        string $description,
        string $scheduledFor,
        string $organizerId
    ): string;
}
