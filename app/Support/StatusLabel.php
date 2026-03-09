<?php

namespace App\Support;

class StatusLabel
{
    public static function for(?string $status): string
    {
        if ($status === null || $status === '') {
            return 'N/A';
        }

        return $status === 'approved'
            ? 'ENROLLED'
            : strtoupper($status);
    }

    public static function forSuperAdmin(?string $status): string
    {
        if ($status === null || $status === '') {
            return 'N/A';
        }

        if ($status === 'approved') {
            return 'ENROLLED';
        }

        if ($status === 'reviewed') {
            return 'PENDING';
        }

        return strtoupper($status);
    }
}
