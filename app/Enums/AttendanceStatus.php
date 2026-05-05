<?php

namespace App\Enums;

enum AttendanceStatus: string
{
    case ON_TIME = 'on_time';
    case LATE = 'late';
    case TO_EARLY = 'to_early';
    case PRESENT = 'present';
    case OVERTIME = 'overtime';
    case OUT_OF_RANGE = 'out_of_range';

    public function label(): string
    {
        return match ($this) {
            self::ON_TIME => 'Tepat Waktu',
            self::LATE => 'Terlambat',
            self::TO_EARLY => 'Terlalu Pagi',
            self::PRESENT => 'Hadir',
            self::OVERTIME => 'Lembur',
            self::OUT_OF_RANGE => 'Di Luar Radius',
        };
    }
}
