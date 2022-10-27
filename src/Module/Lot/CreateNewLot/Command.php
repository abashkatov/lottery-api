<?php

declare(strict_types=1);

namespace App\Module\Lot\CreateNewLot;

use Symfony\Component\Validator\Constraints as Assert;

class Command
{
    #[Assert\NotBlank]
    #[Assert\Length(max: 254)]
    private ?string $title = null;

    #[Assert\NotBlank]
    #[Assert\Length(max: 254)]
    private ?string $description = null;

    #[Assert\NotBlank]
    #[Assert\Length(max: 254)]
    private ?string $address = null;

    #[Assert\NotBlank]
    #[Assert\GreaterThanOrEqual(10)]
    private ?int $priceStart = null;

    #[Assert\NotBlank]
    #[Assert\GreaterThanOrEqual(1)]
    private ?int $priceStep = null;

    #[Assert\NotBlank]
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
