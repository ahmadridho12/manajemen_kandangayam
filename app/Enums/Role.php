<?php

namespace App\Enums;

enum Role
{
    case ADMIN;
    case STAFF;
    case KASUBAG_UMUM_DAN_GUDANG;
    case STAF;

    public function status(): string
    {
        return match ($this) {
            self::ADMIN => 'admin',
            self::STAFF => 'staff',
            self::KASUBAG_UMUM_DAN_GUDANG => 'kasubagumumgudang',
            self::STAF => 'staf',
        };
    }
}
