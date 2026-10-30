<?php

namespace App\Enums;

enum VisitStatus: string
{
    case Pending = 'pending';
    case Approved = 'approved';
    case Rejected = 'rejected';
    case Cancelled = 'cancelled';
    case Closed = 'closed';

    public function label(): string
    {
        return match ($this) {
            self::Pending => 'Zain',
            self::Approved => 'Onartuta',
            self::Rejected => 'Ukatuta',
            self::Cancelled => 'Bertan behera',
            self::Closed => 'Itxita',
        };
    }
}
