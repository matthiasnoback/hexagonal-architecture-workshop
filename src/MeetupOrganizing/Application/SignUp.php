<?php

declare(strict_types=1);

namespace MeetupOrganizing\Application;

use App\Entity\UserType;
use MeetupOrganizing\EmailAddress;

final class SignUp
{
    public function __construct(
        private readonly string $name,
        private readonly string $emailAddress,
        private readonly string $userType,
    ) {
    }

    public function name(): string
    {
        return $this->name;
    }

    public function emailAddress(): EmailAddress
    {
        return new EmailAddress($this->emailAddress);
    }

    public function userType(): UserType
    {
        return UserType::from($this->userType);
    }

    public function validate(): array
    {
        $formErrors = [];
        if ($this->name === '') {
            $formErrors['name'][] = 'Please provide a name';
        }
        if ($this->emailAddress === '') {
            $formErrors['emailAddress'][] = 'Please provide an email address';
        }

        return $formErrors;
    }
}
