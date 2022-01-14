<?php

declare(strict_types=1);

namespace App;

use App\Handler\SignUp;

interface ApplicationInterface
{
    public function signUp(SignUp $command): void;
}
