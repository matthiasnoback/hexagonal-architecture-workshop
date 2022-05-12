<?php
declare(strict_types=1);

namespace MeetupOrganizing;

final class EmailAddress
{
    private string $emailAddress;

    public function __construct(string $emailAddress)
    {
        if (filter_var($emailAddress, FILTER_VALIDATE_EMAIL) === false) {
            throw new \InvalidArgumentException('Invalid email address');
        }

        $this->emailAddress = $emailAddress;
    }

    public function asString(): string
    {
        return $this->emailAddress;
    }
}
