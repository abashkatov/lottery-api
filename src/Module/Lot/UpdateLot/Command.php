<?php

declare(strict_types=1);

namespace App\Module\Lot\UpdateLot;

class Command
{
    private ?string $title = null;

    private ?string $description = null;

    private ?string $address = null;

    private ?int $priceStart = null;

    private ?int $priceStep = null;

    private ?\DateTime $biddingEnd = null;

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function getAddress(): ?string
    {
        return $this->address;
    }

    public function getPriceStart(): ?int
    {
        return $this->priceStart;
    }

    public function getPriceStep(): ?int
    {
        return $this->priceStep;
    }

    public function getBiddingEnd(): ?\DateTime
    {
        return $this->biddingEnd;
    }
}
