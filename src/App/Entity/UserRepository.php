<?php

declare(strict_types=1);

namespace App\Entity;

interface UserRepository
{
    public function nextIdentity(): UserId;

    public function save(User $user): void;

    public function getById(UserId $id): User;

    public function getByEmailAddress(string $emailAddress): User;

    /**
     * @return array<int,string>
     */
    public function findAll(): array;
}
