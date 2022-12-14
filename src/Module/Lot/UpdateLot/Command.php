<?php

declare(strict_types=1);

namespace App\Module\Lot\UpdateLot;

use App\Enum\LotStatus;
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

    #[Assert\Choice(choices: LotStatus::STATUSES)]
    private ?string $status = null;

    #[Assert\Expression("this.isChangeStatusValid()", message: "You cannot change the status of a sold lot")]
    private ?LotStatus $previousStatus = null;

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

    public function setTitle(?string $title): void
    {
        $this->title = $title;
    }

    public function setDescription(?string $description): void
    {
        $this->description = $description;
    }

    public function setAddress(?string $address): void
    {
        $this->address = $address;
    }

    public function setPriceStart(?int $priceStart): void
    {
        $this->priceStart = $priceStart;
    }

    public function setPriceStep(?int $priceStep): void
    {
        $this->priceStep = $priceStep;
    }

    public function setBiddingEnd(?\DateTime $biddingEnd): void
    {
        $this->biddingEnd = $biddingEnd;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(?string $status): void
    {
        $this->status = $status;
    }

    public function getPreviousStatus(): ?LotStatus
    {
        return $this->previousStatus;
    }

    public function setPreviousStatus(?LotStatus $previousStatus): void
    {
        $this->previousStatus = $previousStatus;
    }

    public function isChangeStatusValid(): bool
    {
        if ($this->status === null) {
            return true;
        }
        if ($this->previousStatus !== LotStatus::SALES) {
            return true;
        }
        return $this->previousStatus->value === $this->status;
    }
}
