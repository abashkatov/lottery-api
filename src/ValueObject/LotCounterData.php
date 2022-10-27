<?php

declare(strict_types=1);

namespace App\ValueObject;

class LotCounterData
{
    private int $total;
    private int $my;

    public function __construct(int $total, int $my)
    {
        $this->total = $total;
        $this->my    = $my;
    }

    public function getTotal(): ?int
    {
        return $this->total;
    }

    public function getMy(): ?int
    {
        return $this->my;
    }
}
