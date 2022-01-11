<?php

declare(strict_types=1);

namespace App;

interface ApplicationInterface
{
    public function signUp(SignUp $command): void;
}
