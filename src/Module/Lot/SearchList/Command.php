<?php

declare(strict_types=1);

namespace App\Module\Lot\SearchList;

use Symfony\Component\Validator\Constraints as Assert;

class Command
{
    private const ORDERS = ['id', 'priceStart'];
    private const DEST = ['ASC', 'DESC'];

    #[Assert\GreaterThanOrEqual(0)]
    private int $offset;
    #[Assert\GreaterThanOrEqual(1)]
    private int $limit;
    #[Assert\Choice(choices: self::ORDERS)]
    private ?string $order;
    #[Assert\Choice(choices: self::DEST)]
    private ?string $dest;

    public function __construct(int $page, int $limit, ?string $order, ?string $dest)
    {
        $this->offset = ($page - 1) * $limit;
        $this->limit = $limit;
        $this->order = $order;
        $this->dest = $dest;
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
}
