<?php

namespace App\Enums;

enum UserRole: string
{
    case Student = 'student';
    case ClubLeader = 'club_leader';
    case Admin = 'admin';

    /**
     * @return array<string>
     */
    public static function values(): array
    {
        return array_map(
            static fn (self $role): string => $role->value,
            self::cases(),
        );
    }

    public function label(): string
    {
        return match ($this) {
            self::Student => 'Sinh viên',
            self::ClubLeader => 'Trưởng CLB',
            self::Admin => 'Quản trị',
        };
    }
}
