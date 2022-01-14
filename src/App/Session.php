<?php

declare(strict_types=1);

namespace App;

use App\Entity\User;
use App\Entity\UserId;
use App\Entity\UserRepository;
use App\Entity\UserType;
use Assert\Assert;

final class Session
{
    private const LOGGED_IN_USER_ID = 'logged_in_userId';

    /**
     * @var array<string,mixed>
     */
    private array $sessionData;

    public function __construct(
        private readonly UserRepository $userRepository
    ) {
        if (PHP_SAPI === 'cli') {
            $this->sessionData = [];
        } else {
            session_start();
            $this->sessionData = &$_SESSION;
        }
    }

    public function getLoggedInUser(): ?User
    {
        if (! isset($this->sessionData[self::LOGGED_IN_USER_ID])) {
            return null;
        }

        $loggedInUserId = $this->sessionData[self::LOGGED_IN_USER_ID];
        Assert::that($loggedInUserId)->string();

        return $this->userRepository->getById(UserId::fromString($loggedInUserId));
    }

    public function isLoggedInUserAdmin(): bool
    {
        return $this->isLoggedInUserType(UserType::Administrator);
    }

    public function isLoggedInUserOrganizer(): bool
    {
        return $this->isLoggedInUserType(UserType::Organizer);
    }

    public function isLoggedInUserRegular(): bool
    {
        return $this->isLoggedInUserType(UserType::RegularUser);
    }

    public function isLoggedInUserType(UserType $userType): bool
    {
        $user = $this->getLoggedInUser();
        if ($user === null) {
            return false;
        }

        return $user->userTypeIs($userType);
    }

    public function setLoggedInUserId(UserId $id): void
    {
        $this->sessionData[self::LOGGED_IN_USER_ID] = $id->asString();
    }

    public function isUserLoggedIn(): bool
    {
        return isset($this->sessionData[self::LOGGED_IN_USER_ID]);
    }

    public function isLoggedInUser(string $userId): bool
    {
        if (! isset($this->sessionData[self::LOGGED_IN_USER_ID])) {
            return false;
        }

        $loggedInUserId = $this->sessionData[self::LOGGED_IN_USER_ID];

        return $loggedInUserId === $userId;
    }

    public function logout(): void
    {
        unset($this->sessionData[self::LOGGED_IN_USER_ID]);
    }

    public function addErrorFlash(string $message): void
    {
        $this->addFlash('danger', $message);
    }

    public function addSuccessFlash(string $message): void
    {
        $this->addFlash('success', $message);
    }

    public function getFlashes(): array
    {
        $flashes = $this->sessionData['flashes'] ?? [];
        $flashes = is_array($flashes) ? $flashes : [];

        $this->sessionData['flashes'] = [];

        return $flashes;
    }

    private function addFlash(string $type, string $message): void
    {
        if (! is_array($this->sessionData['flashes'])) {
            $this->sessionData['flashes'] = [];
        }

        $this->sessionData['flashes'][$type][] = $message;
    }
}
