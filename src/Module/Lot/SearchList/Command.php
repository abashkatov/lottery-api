<?php

declare(strict_types=1);

namespace App\Module\Lot\SearchList;

use App\Enum\LotStatus;
use Symfony\Component\Validator\Constraints as Assert;

class Command
{
    private const ORDERS = ['id', 'priceStart', 'currentBid', 'biddingEnd'];
    private const DEST = ['ASC', 'DESC'];

    private int     $offset;
    #[Assert\GreaterThanOrEqual(1)]
    private int     $limit;
    #[Assert\Choice(choices: self::ORDERS)]
    private ?string $order;
    #[Assert\Choice(choices: self::DEST)]
    private ?string $dest;
    private ?bool   $isMy;
    private int     $userId;
    #[Assert\Choice(choices: LotStatus::STATUSES)]
    private ?string $status;
    #[Assert\GreaterThanOrEqual(1)]
    private int     $page;
    private bool    $isOnlyBet;

    public function __construct(int $page, int $limit, ?string $order, ?string $dest, ?string $isMy, ?string $status, bool $isOnlyBet, int $userId)
    {
        $this->offset = ($page - 1) * $limit;
        $this->limit = $limit;
        $this->order = $order;
        $this->dest = $dest;
        $this->isMy = \is_null($isMy)
            ? null
            : $isMy === 'true';
        $this->userId = $userId;
        $this->status = $status;
        $this->page = $page;
        $this->isOnlyBet = $isOnlyBet;
    }

    public function getOffset(): float|int
    {
        return $this->offset;
    }

    public function getLimit(): int
    {
        return $this->limit;
    }

    public function getOrder(): ?string
    {
        return $this->order;
    }

    public function getDest(): ?string
    {
        return $this->dest;
    }

    public function getIsMy(): ?bool
    {
        return $this->isMy;
    }

    public function getUserId(): int
    {
        return $this->userId;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function getPage(): int
    {
        return $this->page;
    }

    public function isOnlyBet(): bool
    {
        return $this->isOnlyBet;
    }
}
