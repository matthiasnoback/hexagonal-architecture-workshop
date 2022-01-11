<?php

declare(strict_types=1);

namespace App\Entity;

enum UserType: string
{
    case RegularUser = 'RegularUser';
    case Organizer = 'Organizer';
    case Administrator = 'Administrator';

    /**
     * @return array<string,string>
     */
    public static function namesAndLabels(): array
    {
        $namesAndLabels = [];
        foreach (self::cases() as $case) {
            $namesAndLabels[$case->name] = $case->label();
        }
        return $namesAndLabels;
    }

    public function label(): string
    {
        return match ($this) {
            self::RegularUser => 'Regular user',
            self::Organizer => 'Organizer',
            self::Administrator => 'Administrator',
        };
    }
}
