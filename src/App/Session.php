<?php

declare(strict_types=1);

namespace App;

use App\Entity\User;
use App\Entity\UserId;
use App\Entity\UserRepository;
use Assert\Assert;

final class Session
{
    private const DEFAULT_USER_ID = 1;

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

    public function getLoggedInUser(): User
    {
        $loggedInUserId = $this->get(self::LOGGED_IN_USER_ID, self::DEFAULT_USER_ID);
        Assert::that($loggedInUserId)->integerish();

        return $this->userRepository->getById(UserId::fromInt((int) $loggedInUserId));
    }

    public function setLoggedInUserId(UserId $id): void
    {
        $this->set(self::LOGGED_IN_USER_ID, $id->asInt());
    }

    public function get(string $key, mixed $defaultValue = null): mixed
    {
        if (isset($this->sessionData[$key])) {
            return $this->sessionData[$key];
        }

        return $defaultValue;
    }

    public function set(string $key, mixed $value): void
    {
        $this->sessionData[$key] = $value;
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
        $flashes = is_array($this->sessionData['flashes']) ? $this->sessionData['flashes'] : [];

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
