<?php

declare(strict_types=1);

namespace App\Enum;

enum LotStatus: string
{
    case OPEN = 'open';
    case SALES = 'sales';
    case CLOSED = 'closed';
    case DRAFT = 'draft';

    public const STATUSES = [
        self::STATUS__OPEN,
        self::STATUS__SALES,
        self::STATUS__CLOSED,
        self::STATUS__DRAFT,
    ];

    private const STATUS__OPEN = 'open';
    private const STATUS__SALES = 'sales';
    private const STATUS__CLOSED = 'closed';
    private const STATUS__DRAFT = 'draft';
}
