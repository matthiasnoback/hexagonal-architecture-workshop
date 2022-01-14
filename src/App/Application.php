<?php

declare(strict_types=1);

namespace App;

use App\Entity\User;
use App\Entity\UserRepository;
use App\Handler\SignUp;

final class Application implements ApplicationInterface
{
    public function __construct(
        private readonly UserRepository $userRepository
    ) {
    }

    public function signUp(SignUp $command): void
    {
        $user = User::create(
            $this->userRepository->nextIdentity(),
            $command->name(),
            $command->emailAddress(),
            $command->userType()
        );

        $this->userRepository->save($user);
    }
}
